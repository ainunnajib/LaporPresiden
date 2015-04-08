from google.appengine.ext import ndb
import hashlib

class UserModel(ndb.Model):
    auth_id = ndb.StringProperty(required=True)
    auth_src = ndb.StringProperty(required=True)

    username = ndb.StringProperty(required=True)
    fullname = ndb.StringProperty(required=True)
    email = ndb.StringProperty()

    def avatar_url_size(self, size=None):
      return '//gravatar.com/avatar/%(hash)s?d=identicon&r=x%(size)s' % {
        'hash': hashlib.md5(
            (self.email or self.username).encode('utf-8')).hexdigest(),
        'size': '&s=%d' % size if size > 0 else '',
        }

    avatar_url = property(avatar_url_size)

    @property
    def id(self):
        return self.key.urlsafe()

    @classmethod
    def by_email(cls, email):
        return cls.query().filter(cls.email == email).get()

    @classmethod
    def by_id(cls, id_me):
        return cls.query().filter(cls.id == id_me).get()


class LaporanModel(ndb.Model):
    judul = ndb.StringProperty(required=True)
    deskripsi = ndb.TextProperty(required=True)
    author = ndb.KeyProperty(kind=UserModel, required=True)
    timestamp = ndb.DateTimeProperty(auto_now_add=True)
    modified = ndb.DateTimeProperty(auto_now=True)

    @property
    def id(self):
        return self.key.urlsafe()


class KomentarModel(ndb.Model):
    laporan = ndb.KeyProperty(kind=LaporanModel, required=True)
    author = ndb.KeyProperty(kind=UserModel, required=True)
    timestamp = ndb.DateTimeProperty(auto_now_add=True)
    deskripsi = ndb.TextProperty(required=True)

    @property
    def id(self):
        return self.key.urlsafe()

    @classmethod
    def by_key(cls, laporan_id):
        laporan = LaporanModel.get_by_id(laporan_id)
        return cls.query().filter(cls.laporan == laporan.key).get()


class LikesModel(ndb.Model):
    laporan = ndb.KeyProperty(kind=LaporanModel, required=True)
    author = ndb.KeyProperty(kind=UserModel, required=True)
    timestamp = ndb.DateTimeProperty(auto_now_add=True)

    @property
    def id(self):
        return self.key.urlsafe()

class DislikesModel(ndb.Model):
    laporan = ndb.KeyProperty(kind=LaporanModel, required=True)
    author = ndb.KeyProperty(kind=UserModel, required=True)
    timestamp = ndb.DateTimeProperty(auto_now_add=True)

    @property
    def id(self):
        return self.key.urlsafe()

