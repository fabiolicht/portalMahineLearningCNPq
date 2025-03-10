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

from tensorflow.keras.utils import custom_object_scope
def custom_Conv2DTranspose(*args, **kwargs):
    kwargs.pop("groups", None)  # Remove 'groups' se existir
    return tf.keras.layers.Conv2DTranspose(*args, **kwargs)

# Carregar o modelo sem o argumento 'groups'
with custom_object_scope({"Conv2DTranspose": custom_Conv2DTranspose, "dice_loss": dice_loss}):
    model = load_model("../../segmentacaoUltrassomFigadoMaligno.h5", compile=False)

model.compile(optimizer="adam", loss="categorical_crossentropy", metrics=["accuracy"])


resolucao = 256
nickname = sys.argv[1]
path = '../'+nickname
# path = nickname
print(path)

# Load and resize images and masks
images = []
for image_file in glob.glob(os.path.join(path)):
    image = load_img(image_file, target_size=(256, 256))
    image_array = img_to_array(image)
    images.append(image_array)
# Convert images and masks to NumPy arrays
images2 = np.array(images)

# Make predictions
predictions = model.predict(images2)
for i in range(len(predictions)):
    prediction = predictions[i]
    # Thresholding para converter em máscara binária
    mask = (prediction[:, :, 0] > 0.5).astype("uint8") * 255
    # Redimensione a máscara para o mesmo tamanho da imagem
    mask = cv2.resize(mask, (images2[i].shape[1], images2[i].shape[0]))
    # Ajuste a transparência diretamente na máscara original
    alpha = 0.1  # Ajuste a transparência conforme necessário
    mask_with_transparency = (mask.astype(float) / 255.0) * alpha
    # Ajuste a cor da área branca da máscara
    color = (250, 50, 50)  # Substitua (0, 0, 255) pela cor desejada (BGR)
    mask_colored = np.zeros_like(images2[i])
    mask_colored[mask == 255] = color
    # Combine a imagem original e a máscara colorida com transparência
    result = cv2.addWeighted(images2[i], 1 - alpha, mask_colored, 1, 0)

    # Salve a saída
    cv2.imwrite(path + ".png", result)
