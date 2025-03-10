import cv2
import numpy as np

# Load the image
image = cv2.imread('images/FabioLicht/01851.png')

# Convert to grayscale (optional)
gray_image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

# Apply thresholding (optional)
thresh = cv2.threshold(gray_image, 127, 255, cv2.THRESH_BINARY)[1]

# Apply edge detection (e.g., Canny edge detector)
edges = cv2.Canny(thresh, 50, 150)

# Check if the edges image is empty
if not np.any(edges):
    print("No edges detected. Adjust thresholding or edge detection parameters.")
    exit()

cv2.imwrite('masked_image.png', edges)
# Find contours on the edge image
_, contours, _ = cv2.findContours(edges, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

# Check if contours were found
if not contours:
    print("No contours found. Adjust edge detection or image preprocessing.")
    exit()

# Select the largest contour (assuming the cone is the largest object)
largest_contour = max(contours, key=cv2.contourArea)

# Create a mask using the largest contour
mask = np.zeros(image.shape, dtype=np.uint8)
cv2.drawContours(mask, [largest_contour], -1, (255, 255, 255), -1)

# Apply the mask to the original image
masked_image = cv2.bitwise_and(image, image, mask=mask)

# Save the masked image (optional)
cv2.imwrite('masked_image.png', masked_image)
