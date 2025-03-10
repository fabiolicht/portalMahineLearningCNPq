#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Fri Dec  1 10:19:06 2023

@author: fabiolicht
"""

import tensorflow as tf
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import load_img
import numpy as np
import glob
import os
import sys

nickname = sys.argv[1]
model = load_model("../../classificacaoCelularColon.h5", compile=False)
model.compile(optimizer="adam", loss="categorical_crossentropy", metrics=["accuracy"])
path = '../'+nickname  # +"/*.png"
#path = nickname  # +"/*.png"
# print(path)
images = []
names = []
# Load and resize the image
for image_file in glob.glob(os.path.join(path)):
    image = load_img(image_file, target_size=(256, 256))
    images.append(image)
    names.append(os.path.basename(image_file))
qtdImg = (len(images))
# image_width, image_height = image.size
# print("width ", image_width)
# print("height ", image_height)

for i in range(qtdImg):
    # Convert image to NumPy array
    image_array = tf.keras.preprocessing.image.img_to_array(images[i])
    # Convert to 4D array (add batch dimension)
    image_array = tf.expand_dims(image_array, axis=0)
    # Get the current data type
    current_dtype = image_array.dtype
    # Convert to float32
    new_dtype = np.float32
    image_array = np.asarray(image_array, dtype=new_dtype)
    # Make prediction using the formatted image array
    prediction = model.predict(image_array, verbose=0)
    # Get the class index and class name
    class_index = np.argmax(prediction)
    # print("\nThe file ",names[i], "is ", end="")
    if (class_index == 0):
        print("NormalQQQQQQ", end='')
    if (class_index == 1):
        print("Tumor_MalignoQQQQQQ", end='')
    #if (class_index == 2):
    #    print("Tumor BenignoQQQQQQ", end='')

    class_probabilities = prediction[0] * 100  # Convert to percentages
    
    # Print the probabilities
    class_names = ['Normal', 'Tumor_Maligno']
    for i, probability in enumerate(class_probabilities):
        print(f"QQQHá_{probability:.2f}___de_ser_{class_names[i]}")
        