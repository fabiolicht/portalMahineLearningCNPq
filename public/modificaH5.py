import sys
import json
from tensorflow.keras.models import Model
from tensorflow.keras.layers import Input, Conv2D, MaxPooling2D, Flatten, Dense  # Substitua pelas camadas reais do seu modelo
from tensorflow.keras.preprocessing import image
import numpy as np
from tensorflow.keras.models import load_model
from PIL import Image

def preprocess_image(image_path, target_size=(256, 256)):
    img = Image.open(image_path)
    if img.mode != 'RGB':
        img = img.convert('RGB')
    img = img.resize(target_size)

    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array /= 255.0

    return img_array

# Função para recriar o modelo manualmente
def create_model():
    input_layer = Input(shape=(256, 256, 3), name="input_layer")  # Ajuste o shape conforme necessário
    x = Conv2D(32, (3, 3), activation="relu")(input_layer)
    x = MaxPooling2D((2, 2))(x)
    x = Flatten()(x)
    output_layer = Dense(1, activation="sigmoid")(x)  # Ajuste conforme a saída do seu modelo
    model = Model(inputs=input_layer, outputs=output_layer)
    
    return model

# Função para validar a imagem
def validate_image(model, image_path):
    img_array = preprocess_image(image_path)
    prediction = model.predict(img_array)

    is_valid = bool(np.argmax(prediction) == 1)  # Exemplo: retorno True se for do tipo esperado

    return json.dumps({"valid": is_valid})

if __name__ == "__main__":
    image_path = sys.argv[1]
    model_path = "../../imagens.h5"  # Atualize com o caminho correto

    # Cria o modelo e carrega os pesos
    model = create_model()
    model.load_weights(model_path)  # Apenas carrega os pesos

    result = validate_image(model, image_path)

    print(result)

