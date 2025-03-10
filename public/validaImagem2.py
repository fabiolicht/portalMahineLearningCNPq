#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Mon Sep 30 09:44:10 2024

@author: fabiolicht
"""

from tensorflow.keras.models import load_model
import numpy as np
from PIL import Image, ImageDraw, ImageFont
import glob
import os
import sys

# Função para carregar e preprocessar a imagem
def preprocess_image(image_path, target_size=(256, 256)):
    img = Image.open(image_path).convert('RGB')  # Carregar a imagem em RGB (3 canais)
    img = img.resize(target_size)                # Redimensionar para o tamanho de entrada esperado pelo modelo
    img_array = np.array(img)                    # Converter a imagem para um array NumPy
    img_array = img_array.astype('float32') / 255.0  # Normalizar os valores dos pixels entre [0, 1]
    
    # Agora o img_array tem forma (256, 256, 3), vamos expandir corretamente
    img_array = np.expand_dims(img_array, axis=0)  # Adicionar a dimensão do lote, resultado será (1, 256, 256, 3)
    
    return img_array, img

# Função para calcular o erro de reconstrução
def reconstruction_error(autoencoder, images):
    # Usar o autoencoder para reconstruir as imagens
    reconstructed_images = autoencoder.predict(images)
    
    # Calcular o erro absoluto médio entre a imagem original e a reconstruída
    error = np.mean(np.abs(images - reconstructed_images), axis=(1, 2, 3))
    
    return error

# Carregar o modelo
autoencoder = load_model("../../imagens.h5")

image_path = sys.argv[1]
# Iterar sobre as 10 primeiras imagens de teste
# Preprocessar a imagem
new_image, original_image = preprocess_image(image_path)

# Usar o autoencoder para inferência
error = reconstruction_error(autoencoder, new_image)

# Definir um limiar de erro para decidir a aceitação/rejeição da imagem
error_threshold = 0.5  # Esse valor pode ser ajustado com base em experimentos
print("ERRO: ",error)
# Verificar se a imagem foi aceita ou rejeitada
if error > error_threshold:
    print("False")
else:
    print("True")
