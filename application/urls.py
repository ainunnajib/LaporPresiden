from flask import render_template
from application import app
from application import views


app.add_url_rule('/_ah/warmup', 'warmup', view_func=views.warmup)

# Home page
app.add_url_rule('/', 'home', view_func=views.home)


app.add_url_rule('/login', 'fb_login', view_func=views.fb_login)
app.add_url_rule('/login/authorized', 'fb_login_authorized', view_func=views.fb_login_authorized)
app.add_url_rule('/logout', 'logout', view_func=views.logout)


app.add_url_rule('/laporan', 'daftar_laporan', view_func=views.daftar_laporan, methods=['GET'])
app.add_url_rule('/laporan/add', 'tulis_laporan', view_func=views.tulis_laporan, methods=['GET', 'POST'])
app.add_url_rule('/laporan/<int:laporan_id>', 'lihat_laporan', view_func=views.lihat_laporan, methods=['GET'])
app.add_url_rule('/laporan/<int:laporan_id>/edit', 'edit_laporan', view_func=views.edit_laporan, methods=['GET', 'POST'])
app.add_url_rule('/laporan/<int:laporan_id>/delete', view_func=views.delete_laporan, methods=['GET'])

app.add_url_rule('/laporan/<int:laporan_id>/likes/yes', 'likes_yes', view_func=views.likes_yes, methods=['GET'])
app.add_url_rule('/laporan/<int:laporan_id>/likes/no', 'likes_no', view_func=views.likes_no, methods=['GET'])
app.add_url_rule('/laporan/<int:laporan_id>/dislikes/yes', 'dislikes_yes', view_func=views.dislikes_yes, methods=['GET'])
app.add_url_rule('/laporan/<int:laporan_id>/dislikes/no', 'dislikes_no', view_func=views.dislikes_no, methods=['GET'])



app.add_url_rule('/komentar/<int:laporan_id>', 'tulis_komentar', view_func=views.tulis_komentar, methods=['POST'])
app.add_url_rule('/komentar/<int:komentar_id>/delete', 'delete_komentar', view_func=views.delete_komentar, methods=['GET'])


app.add_url_rule('/examples/cached', 'cached_examples', view_func=views.cached_examples, methods=['GET'])
app.add_url_rule('/admin_only', 'admin_only', view_func=views.admin_only)

# Handle 404 errors
@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html'), 404

# Handle 500 errors
@app.errorhandler(500)
def server_error(e):
    return render_template('500.html'), 500

