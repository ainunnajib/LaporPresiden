from flaskext import wtf
from flaskext.wtf import validators
from wtforms.ext.appengine.ndb import model_form

from .models import LaporanModel
from .models import KomentarModel

#class ClassicExampleForm(wtf.Form):
#    example_name = wtf.TextField('Name', validators=[validators.Required()])
#    example_description = wtf.TextAreaField('Description', validators=[validators.Required()])

class ClassicLaporanForm(wtf.Form):
    judul = wtf.TextField('Judul', validators=[validators.Required()])
    deskripsi = wtf.TextAreaField('Description', validators=[validators.Required()])

class ClassicKomentarForm(wtf.Form):
    deskripsi = wtf.TextAreaField('Description', validators=[validators.Required()])


# App Engine ndb model form example
LaporanForm = model_form(LaporanModel, wtf.Form, field_args={
    'judul': dict(validators=[validators.Required()]),
    'deskripsi': dict(validators=[validators.Required()]),
})

KomentarForm = model_form(KomentarModel, wtf.Form, field_args={
    'deskripsi': dict(validators=[validators.Required()]),
})
