import tensorflow as tf
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import load_img, img_to_array
import numpy as np
import glob
import os
import cv2
from datetime import date, datetime
import os.path
import sys

# Defina a função dice_loss


def dice_loss(y_true, y_pred):
    numerator = 2 * tf.reduce_sum(y_true * y_pred)
    denominator = tf.reduce_sum(y_true + y_pred)
    dice_coef = numerator / (denominator + tf.keras.backend.epsilon())
    return 1 - dice_coef


nickname = sys.argv[1]
data = datetime.now().strftime('%d.%m.%Y_%H.%M')
path = "images/"+nickname+"/*.png"

while os.path.exists('output') == False:
    os.makedirs("output")
while os.path.exists('output/UNET') == False:
    os.makedirs("output/UNET")
    os.makedirs("output/INCUNET")
    os.makedirs("output/RESUNET")
    os.makedirs("output/STAUNET")

for r in range(4):
    if (r == 0):
        rede = 'UNET'
    elif (r == 1):
        rede = 'INCUNET'
    elif (r == 2):
        rede = 'RESUNET'
    else:
        rede = 'STAUNET'

    # Carregue o modelo com a função de perda personalizada
    model = load_model(rede+".hdf5", custom_objects={'dice_loss': dice_loss})
    resolucao = 256
    # Load and resize images and masks
    images = []
    masks = []
    for image_file, mask_file in zip(glob.glob(os.path.join("test/images/*.png")), glob.glob(os.path.join("test/masks/*.png"))):
        image = load_img(image_file, target_size=(resolucao, resolucao))
        mask = load_img(mask_file, target_size=(
            resolucao, resolucao), color_mode="grayscale")
        # Convert images and masks to NumPy arrays
        image_array = img_to_array(image)
        # Normaliza as máscaras para o intervalo [0, 1]
        mask_array = img_to_array(mask) / 255.0
        images.append(image_array)
        masks.append(mask_array)

    # Convert images and masks to NumPy arrays
    images2 = np.array(images)
    masks2 = np.array(masks)

    # Make predictions
    predictions = model.predict(images2)
    os.makedirs("output/"+rede+"/"+data)
    # Process output
    # # Aqui, vamos salvar as máscaras previstas como imagens
    for i in range(len(predictions)):
        prediction = predictions[i]
        # Thresholding para converter em máscara binária
        mask = (prediction[:, :, 0] > 0.5).astype("uint8") * 255
        # Redimensione a máscara para o mesmo tamanho da imagem
        mask = cv2.resize(mask, (images[i].shape[1], images[i].shape[0]))
        # Ajuste a transparência diretamente na máscara original
        alpha = 0.1  # Ajuste a transparência conforme necessário
        mask_with_transparency = (mask.astype(float) / 255.0) * alpha
        # Ajuste a cor da área branca da máscara
        color = (50, 100, 100)  # Substitua (0, 0, 255) pela cor desejada (BGR)
        mask_colored = np.zeros_like(images[i])
        mask_colored[mask == 255] = color
        # Combine a imagem original e a máscara colorida com transparência
        result = cv2.addWeighted(images[i], 1 - alpha, mask_colored, 1, 0)

        # Salve a saída
        cv2.imwrite("output/"+rede+"/"+data +
                    "/prediction_" + str(i) + ".png", result)

    for i in range(len(predictions)):
        # Carregar a máscara original e convertê-la para uma imagem colorida
        original_mask = (masks[i] * 255).astype(np.uint8)
        original_mask_colored = cv2.cvtColor(original_mask, cv2.COLOR_GRAY2BGR)
        # Máscara predita a partir das previsões
        prediction = predictions[i]
        predicted_mask = (prediction[:, :, 0] > 0.5).astype("uint8") * 255
        # Converta a máscara predita para uma imagem colorida
        predicted_mask_colored = cv2.cvtColor(
            predicted_mask, cv2.COLOR_GRAY2BGR)
        # Empilhar as imagens lado a lado
        combined_image = np.hstack(
            [images[i], original_mask_colored, predicted_mask_colored])
        # Salvar a imagem combinada
        cv2.imwrite("output/"+rede+"/"+data +
                    "/combined_prediction_" + str(i) + ".png", combined_image)
