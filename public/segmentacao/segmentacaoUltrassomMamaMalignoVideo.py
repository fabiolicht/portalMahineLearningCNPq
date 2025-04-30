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
from tensorflow.keras.utils import custom_object_scope

# Defina a função dice_loss


def dice_loss(y_true, y_pred):
    numerator = 2 * tf.reduce_sum(y_true * y_pred)
    denominator = tf.reduce_sum(y_true + y_pred)
    dice_coef = numerator / (denominator + tf.keras.backend.epsilon())
    return 1 - dice_coef


def custom_Conv2DTranspose(*args, **kwargs):
    kwargs.pop("groups", None)  # Remove 'groups' se existir
    return tf.keras.layers.Conv2DTranspose(*args, **kwargs)

# Carregar o modelo sem o argumento 'groups'
with custom_object_scope({"Conv2DTranspose": custom_Conv2DTranspose, "dice_loss": dice_loss}):
    model = load_model("../../../segmentacaoUltrassomMamaMaligno.h5", compile=False)

model.compile(optimizer="adam", loss="categorical_crossentropy", metrics=["accuracy"])

def extrairFotos(caminho_video, intervalo_segundos=2, pasta_saida="frames"):
    os.makedirs(pasta_saida, exist_ok=True)

    for arquivo in os.listdir(pasta_saida):
            caminho_arquivo = os.path.join(pasta_saida, arquivo)
            if os.path.isfile(caminho_arquivo):
                os.remove(caminho_arquivo)
            
    video = cv2.VideoCapture(caminho_video)
    if not video.isOpened():
        print("Erro ao abrir o vídeo.")
        return

    fps = video.get(cv2.CAP_PROP_FPS)
    intervalo_frames = int(fps * intervalo_segundos)

    frame_atual = 0
    contador = 0

    while True:
        sucesso, frame = video.read()
        if not sucesso:
            break

        if frame_atual % intervalo_frames == 0:
            nome_arquivo = os.path.join(pasta_saida, f"frame_{contador:04d}.jpg")
            cv2.imwrite(nome_arquivo, frame)
            contador += 1

        frame_atual += 1

    video.release()
    print(f"Extração finalizada: {contador} imagens salvas em '{pasta_saida}'.")

resolucao = 256
nickname = sys.argv[1]
path = '../../'+nickname
#path = nickname
print(path)
extrairFotos(path, intervalo_segundos=2)
path=path+'/frames/'

image_files = sorted(glob.glob(os.path.join(path, '*')))  # pega todos os arquivos da pasta
images=[]

for image_file in image_files:
    image = load_img(image_file, target_size=(256, 256))
    image_array = img_to_array(image)
    images.append(image_array)

images2 = np.array(images)

# Fazer as previsões
predictions = model.predict(images2)

# Lista para armazenar os resultados segmentados
segmented_images = []

for i in range(len(predictions)):
    prediction = predictions[i]
    mask = (prediction[:, :, 0] > 0.5).astype("uint8") * 255
    mask = cv2.resize(mask, (images2[i].shape[1], images2[i].shape[0]))

    alpha = 0.1
    color = (250, 50, 50)
    mask_colored = np.zeros_like(images2[i])
    mask_colored[mask == 255] = color

    result = cv2.addWeighted(images2[i].astype(np.uint8), 1 - alpha, mask_colored.astype(np.uint8), 1, 0)

    segmented_images.append(result)

# Combinar todas as imagens em uma única imagem (horizontal ou vertical)
final_image = cv2.vconcat(segmented_images)  # ou use cv2.hconcat para juntar na horizontal

# Salvar a imagem final
cv2.imwrite(os.path.join(path, 'segmentacoes_combinadas.png'), final_image)