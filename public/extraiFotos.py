#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Wed Apr  9 13:18:32 2025

@author: fabiolicht
"""

import cv2
import os

def extrairFotos(caminho_video, intervalo_segundos=2, pasta_saida="videos/frames"):
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

extrairFotos("videos/video.mp4", intervalo_segundos=2)

