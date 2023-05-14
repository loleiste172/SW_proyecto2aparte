from flask import Flask, jsonify, request
from firebase import firebase
import hashlib
import re
from fastapi import Body, FastAPI, status
from fastapi.responses import JSONResponse
from flask import Response
from string import punctuation

firebase = firebase.FirebaseApplication("https://pr06-3e340-default-rtdb.firebaseio.com/") #base de datos
app = Flask(__name__)

# cred_obj = firebase_admin.credentials.Certificate('C:/Users/MARIAFLORESSANCHEZ/Documents/WS/proyecto_p2/pr06-3e340-firebase-adminsdk-b8tti-1001a49c27.json')
# default_app = firebase_admin.initialize_app(cred_obj, {
# 	'databaseURL':'https://pr06-3e340-default-rtdb.firebaseio.com/'
# 	})


#validar email
def is_valid_email(text):
    pattern = r'^[a-z][a-z0-9\-_\.]+[@][a-z]+[.][a-z]{1,3}$'
    return re.search(pattern, text)

#validar contraseña
def valid_password(text):
    errores = ""
    if len(text) > 8 :
      if any([c.isdigit() for c in text]):
          if any([c.islower() for c in text]):
              if any([c.isupper() for c in text]):
                    if any([True if c in punctuation else False for c in text]):
                       return errores
                    else:
                      errores += "La contraseña debe tener un caracter especial"
              else: 
                 errores += "La contraseña debe tener una mayuscula"
          else: 
              errores += "La contraseña debe tener al menos una minuscula"
      else:
           errores += "La contraseña debe tener al menos 1 digito"
    else:
        errores += "La contraseña debe ser mayor a 8 caracteres"
    return errores                 
                  
                  


# Create 
@app.route('/usuarios', methods=['POST'])
def addUsuario():
    correo = request.json['correo']
    correo_insert = correo.replace(".", "-")
    pass_encript = hashlib.md5(request.json['pass'].encode("utf-8")).hexdigest() #cifrado con md5

    usuario_existe = firebase.get('/usuarios_sistema/'+correo_insert,'correo')
    if(usuario_existe):
        #return jsonify({'message': 'Usuario ya existe'})
        return Response("{'status':'error', 'message':'Correo ya existe, por favor crea otro'}", status=422, mimetype='application/json')
    else :
            if is_valid_email(request.json['correo']):
                if valid_password(request.json['pass']) == "":
                    datos = {
                        'aplicacion': request.json['aplicacion'],
                        'correo': request.json['correo'],
                        'name': request.json['name'],
                        'pass': pass_encript,
                    }
                    res=firebase.patch('/usuarios_sistema/'+correo_insert, datos) 
                else:
                     return Response("{'status':'error', 'message':'"+valid_password(request.json['pass'])+"'}", status=422, mimetype='application/json')
            else :
             #return jsonify({'message': 'Correo invalido'})   
             return Response("{'status':'error', 'message':'Correo invalido'}", status=422, mimetype='application/json')
  
    #return jsonify({'message': 'Usuario Agregado'})
    #return JSONResponse(status_code=status.HTTP_201_CREATED, content='Usuario Agregado')
    #return responses
    return Response("{'status':'success'}", status=201, mimetype='application/json')
    #return jsonify(status_code=status.HTTP_201_CREATED, content={"message": "Usuario Agregado"})   
    #return jsonify(status_code=status.HTTP_201_CREATED, content=item)
    #return self.assertEqual(response.status_code, status.HTTP_201_CREATED)
    
if __name__ == '__main__':
    app.run(debug=True, port=5000)
