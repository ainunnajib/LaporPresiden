from google.appengine.api import users
from google.appengine.runtime.apiproxy_errors import CapabilityDisabledError

from flask import request, render_template, flash, url_for, redirect

from flask_cache import Cache

from application import app
from decorators import login_required, admin_required, get_logged
from forms import *
from models import *

from flask import Flask, redirect, url_for, session, request
from flask_oauth import OAuth


SECRET_KEY = 'laporpresiden.dev.team'
DEBUG = True
FACEBOOK_APP_ID = '119951971443590'
FACEBOOK_APP_SECRET = '041ade28808267956a09d6e4805ddbec'


app = Flask(__name__)
app.debug = DEBUG
app.secret_key = SECRET_KEY
oauth = OAuth()

facebook = oauth.remote_app('facebook',
    base_url='https://graph.facebook.com/',
    request_token_url=None,
    access_token_url='/oauth/access_token',
    authorize_url='https://www.facebook.com/dialog/oauth',
    consumer_key=FACEBOOK_APP_ID,
    consumer_secret=FACEBOOK_APP_SECRET,
    request_token_params={'scope': 'email'}
)



cache = Cache(app)

def home():
    return redirect(url_for('daftar_laporan'))

def fb_login():
    return facebook.authorize(callback=url_for('fb_login_authorized',
        next=request.args.get('next') or request.referrer or None,
        _external=True))

@facebook.authorized_handler
def fb_login_authorized(resp):
    if resp is None:
        return 'Access denied: reason=%s error=%s' % (
            request.args['error_reason'],
            request.args['error_description']
        )
    session['oauth_token'] = (resp['access_token'], '')
    me = facebook.get('/me')
    saya = UserModel.by_email(me.data['email'])
    if not saya:
        gw = UserModel(
            auth_id=me.data['id'],
            auth_src="facebook",
            username=str(me.data['name']).replace(" ", "_"),
            fullname=me.data['name'],
            email=me.data['email']
        )
        try:
            gw.put()
            gw_id = gw.username
            flash(u'Akun baru dengan username `%s` berhasil dibuat.' % gw_id, 'success')
        except CapabilityDisabledError:
            flash(u'App Engine Datastore is currently in read-only mode.', 'info')

    session['me'] = me.data['email']

    flash(u'Logged in as %s' % (me.data['name']), 'info')

    return redirect(url_for('home'))

@facebook.tokengetter
def get_facebook_oauth_token():
    return session.get('oauth_token')

@login_required
def logout():
  session.clear()
  return redirect(url_for('home'))      



def daftar_laporan():
    t_laporans = LaporanModel.query().order(LaporanModel.timestamp)
    laporans = []
    for tl in t_laporans:
      tl.user = UserModel.get_by_id(tl.author.id())
      tl.total_komentar = KomentarModel.query().filter(KomentarModel.laporan == tl.key).count()
      tl.total_likes = LikesModel.query().filter(LikesModel.laporan == tl.key).count()
      tl.total_dislikes = DislikesModel.query().filter(DislikesModel.laporan == tl.key).count()
      laporans.append(tl)

    form = LaporanForm()
    return render_template('daftar_laporan.html', laporans=laporans, form=form, user=get_logged())


@login_required
def tulis_laporan():
    laporan = LaporanModel()
    form = LaporanForm()
    if request.method == "POST":
        #if form.validate_on_submit():
          laporan = LaporanModel(
              judul=form.judul.data,
              deskripsi=form.deskripsi.data,
              author=get_logged().key
          )
          try:  
            laporan.put()
            laporan_id = laporan.key.id()
            flash(u'Laporan %s successfully saved.' % laporan_id, 'success')
          except:
            flash(u'Laporan failed saved.', 'failed')

          return redirect(url_for('daftar_laporan'))
    return render_template('tulis_laporan.html', laporan=laporan, form=form, user=get_logged())


@login_required
def edit_laporan(laporan_id):
    laporan = LaporanModel.get_by_id(laporan_id)
    laporan_user = UserModel.get_by_id(laporan.author.id())
    form = LaporanForm(obj=laporan)
    user = get_logged()

    if user != None and laporan.author.id() != user.key.id():
      flash(u'Anda tidak berhak mengedit laporan id %s milik %s' % (laporan_id, laporan_user.username), 'info')
      return redirect(url_for('home'))

    if request.method == "POST":
        #if form.validate_on_submit():
            laporan.judul = form.data.get('judul')
            laporan.deskripsi = form.data.get('deskripsi')
            laporan.put()
            flash(u'Laporan %s successfully saved.' % laporan_id, 'success')
            return redirect(url_for('home'))

    return render_template('edit_laporan.html', laporan=laporan, form=form, user=user)

def lihat_laporan(laporan_id):
    laporan = LaporanModel.get_by_id(laporan_id)

    t_komentars = KomentarModel.query().order(KomentarModel.timestamp).filter(KomentarModel.laporan == laporan.key).fetch()
    komentars = []
    for komentar in t_komentars:
      komentar.user = UserModel.get_by_id(komentar.author.id())
      komentars.append(komentar)

    laporan.user = UserModel.get_by_id(laporan.author.id())
    form_komentar = KomentarForm()
    user = get_logged()

    laporan.total_komentar = KomentarModel.query().filter(KomentarModel.laporan == laporan.key).count()
    laporan.total_likes = LikesModel.query().filter(LikesModel.laporan == laporan.key).count()
    laporan.total_dislikes = DislikesModel.query().filter(DislikesModel.laporan == laporan.key).count()

    try: laporan.my_likes = LikesModel.query().filter(LikesModel.laporan == laporan.key, LikesModel.author == user.key).count()
    except: laporan.my_likes = 0

    try: laporan.my_dislikes = DislikesModel.query().filter(DislikesModel.laporan == laporan.key, DislikesModel.author == user.key).count()
    except: laporan.my_dislikes = 0

    return render_template('lihat_laporan.html', laporan=laporan, form_komentar=form_komentar, komentars=komentars, user=get_logged())

@login_required
def delete_laporan(laporan_id):
    laporan = LaporanModel.get_by_id(laporan_id)
    user = get_logged()

    if laporan.author.id() != user.key.id():
      flash(u'Anda tidak berhak mengedit laporan id %s.' % laporan_id, 'info')
      return redirect(url_for('home'))

    try:
      laporan.key.delete()
      flash(u'Laporan %s successfully deleted.' % laporan_id, 'info')
      return redirect(url_for('home'))
    except CapabilityDisabledError:
      flash(u'App Engine Datastore is currently in read-only mode.', 'info')
      return redirect(url_for('daftar_laporan'))




@login_required
def tulis_komentar(laporan_id):
    form = KomentarForm()
    komentar = KomentarModel(
      laporan=LaporanModel.get_by_id(laporan_id).key,
      deskripsi=form.deskripsi.data,
      author=get_logged().key
    )
    try:  
      komentar.put()
      komentar_id = komentar.key.id()
      flash(u'Komentar ID %s successfully psted.' % komentar_id, 'success')
    except:
      flash(u'Komentar failed posted.', 'failed')

    return redirect(url_for('lihat_laporan', laporan_id=laporan_id))

@login_required
def delete_komentar(komentar_id):
    komentar = KomentarModel.get_by_id(komentar_id)
    laporan_id = komentar.laporan.id()
    print komentar, laporan_id

    user = get_logged()

    if komentar.author.id() != user.key.id():
      flash(u'Anda tidak berhak menghapus komentar id %s.' % komentar_id, 'info')
      return redirect(url_for('lihat_laporan', laporan_id=komentar.laporan.id()))

    try:
      komentar.key.delete()
      flash(u'Komentar ID %s successfully deleted.' % komentar_id, 'info')
      return redirect(url_for('lihat_laporan', laporan_id=komentar.laporan.id()))
    except CapabilityDisabledError:
      flash(u'App Engine Datastore is currently in read-only mode.', 'info')
      return redirect(url_for('lihat_laporan', laporan_id=komentar.laporan.id()))


@login_required
def likes_yes(laporan_id):
    user = get_logged()
    laporan = LaporanModel.get_by_id(laporan_id)
    my_likes = LikesModel.query().filter(LikesModel.laporan == laporan.key, LikesModel.author == user.key)

    if my_likes.count() == 0:
      likes = LikesModel(
        laporan=LaporanModel.get_by_id(laporan_id).key,
        author=get_logged().key
      )
      try:  
        likes.put()
        flash(u'Likes Anda telah tersimpan!.', 'success')

        try: DislikesModel.query().filter(DislikesModel.laporan == laporan.key, DislikesModel.author == user.key).get().key.delete()
        except: pass
      except:
        flash(u'Likes Anda gagal disimpan!.', 'failed')

    return redirect(url_for('lihat_laporan', laporan_id=laporan_id))

@login_required
def likes_no(laporan_id):
    user = get_logged()
    laporan = LaporanModel.get_by_id(laporan_id)
    my_likes = LikesModel.query().filter(LikesModel.laporan == laporan.key, LikesModel.author == user.key)

    if my_likes.count() > 0:
      try:  
        my_likes.get().key.delete()
        flash(u'Likes Anda telah dibatalkan!.', 'success')
      except:
        flash(u'Likes Anda gagal dibatalkan!.', 'failed')

    return redirect(url_for('lihat_laporan', laporan_id=laporan_id))

@login_required
def dislikes_yes(laporan_id):
    user = get_logged()
    laporan = LaporanModel.get_by_id(laporan_id)
    my_dislikes = DislikesModel.query().filter(DislikesModel.laporan == laporan.key, DislikesModel.author == user.key)


    if my_dislikes.count() == 0:
      dislikes = DislikesModel(
        laporan=LaporanModel.get_by_id(laporan_id).key,
        author=get_logged().key
      )
      try:  
        dislikes.put()
        flash(u'Dislikes Anda telah tersimpan!.', 'success')
        try:
          LikesModel.query().filter(LikesModel.laporan == laporan.key, LikesModel.author == user.key).get().key.delete()
        except: pass

      except:
        flash(u'Dislikes Anda gagal disimpan!.', 'failed')

    return redirect(url_for('lihat_laporan', laporan_id=laporan_id))

@login_required
def dislikes_no(laporan_id):
    user = get_logged()
    laporan = LaporanModel.get_by_id(laporan_id)
    my_dislikes = DislikesModel.query().filter(DislikesModel.laporan == laporan.key, DislikesModel.author == user.key)

    if my_dislikes.count() > 0:
      try:  
        my_dislikes.get().key.delete()
        flash(u'Dislikes Anda telah dibatalkan!.', 'success')
      except:
        flash(u'Dislikes Anda gagal dibatalkan!.', 'failed')

    return redirect(url_for('lihat_laporan', laporan_id=laporan_id))



@admin_required
def admin_only():
    return 'Super-seekrit admin page.'


@cache.cached(timeout=60)
def cached_examples():
    examples = ExampleModel.query()
    return render_template('list_examples_cached.html', examples=examples)


def warmup():
    """App Engine warmup handler
    See http://code.google.com/appengine/docs/python/config/appconfig.html#Warming_Requests

    """
    return ''

