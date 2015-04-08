from functools import wraps
from google.appengine.api import users
from models import *
from flask import session, redirect, request, abort, url_for
from flask import request, render_template, flash, url_for, redirect

def get_logged():
    email_me = session.get('me')   
    return UserModel.by_email(email_me)


def login_required(func):
    @wraps(func)
    def decorated_view(*args, **kwargs):
        if not get_logged():
            #return redirect(url_for('fb_login'))
            return render_template('login.html', user=get_logged())
        return func(*args, **kwargs)
    return decorated_view


def admin_required(func):
    @wraps(func)
    def decorated_view(*args, **kwargs):
        if users.get_current_user():
            if not users.is_current_user_admin():
                abort(401)  # Unauthorized
            return func(*args, **kwargs)
        return redirect(users.create_login_url(request.url))
    return decorated_view
