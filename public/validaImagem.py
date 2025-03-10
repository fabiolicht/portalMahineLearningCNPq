import sys
import json
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image
import numpy as np
from PIL import Image

# Função para carregar e preprocessar a imagem
def preprocess_image(image_path, target_size=(256, 256)):
    # Abrir a imagem usando PIL
    img = Image.open(image_path)
    
    # Converter para RGB caso tenha mais de 3 canais (ex.: RGBA)
    if img.mode != 'RGB':
        img = img.convert('RGB')
    
    # Redimensionar para o tamanho esperado pelo modelo
    img = img.resize(target_size)
    
    # Converter a imagem para um array numpy
    img_array = image.img_to_array(img)
    
    # Expandir as dimensões para corresponder ao formato que o modelo espera: (1, 256, 256, 3)
    img_array = np.expand_dims(img_array, axis=0)
    
    # Normalizar os valores da imagem (opcional, depende de como o modelo foi treinado)
    #img_array /= 255.0
    
    return img_array

# Função para validar a imagem usando o modelo treinado
def validate_image(model, image_path):
    # Preprocessar a imagem
    img_array = preprocess_image(image_path)
    
    # Fazer a predição com o modelo
    prediction = model.predict(img_array)
    
    # Verificar o resultado (supondo que o modelo retorne uma probabilidade)
    is_valid = bool(np.argmax(prediction) == 1)  # Exemplo: retorno True se for do tipo esperado
    
    # Retornar a resposta em formato JSON
    return json.dumps({"valid": is_valid})

if __name__ == "__main__":
    # Caminho da imagem passada como argumento
    image_path = sys.argv[1]
    
    # Carregar o modelo treinado
    model_path = "../../imagens.h5" 
    model = load_model(model_path)
    
    # Validar a imagem
    result = validate_image(model, image_path)
    
    # Imprimir o resultado para ser capturado pelo PHP
    print(result)


