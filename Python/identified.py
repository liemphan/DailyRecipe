import keras
from keras_preprocessing import image
import matplotlib.pyplot as plt
import numpy as np
from keras.models import load_model



model=load_model("Python/fruit_n_veg_model.h5")

name=['Bắp', 'Bắp cải', 'Bí ngô', 'Bí xanh', 'Bông cải xanh', 'Bơ', 'Cà chua', 'Cà rốt', 'Cà tím', 'Cá hồi', 'Cá ngừ', 'Cải bó xôi', 'Cải trắng', 'Chanh', 'Chuối', 'Củ cải trắng', 'Củ hành', 'Dâu', 'Dưa leo', 'Dứa', 'Gừng', 'Hành tím', 'Hàu', 'Hạnh nhân', 'Khế', 'Khoai lang', 'Khoai tây', 'Khổ Qua', 'Kiwi', 'Lựu', 'Óc chó', 'Ớt', 'Ớt chuông', 'Su Hào', 'Súp lơ', 'Táo', 'Thanh long', 'Thịt bò', 'Thịt heo', 'Tôm', 'Tỏi', 'Xà lách', 'Xoài', 'Đậu xanh', 'Đu đủ']

def process_image(image_path):
    img = image.load_img(image_path, target_size=(224, 224, 3))
    plt.imshow(img)
    plt.show()
    x = image.img_to_array(img)
    x = np.expand_dims(x, axis=0)
    images = np.vstack([x])
    pred = model.predict(images, batch_size=32)
    print("Predicted: " + name[np.argmax(pred)])
process_image('Python/img.png')