const dropArea = document.getElementById("drop-area");
const inputFile = document.getElementById("input-file");
const imageView = document.getElementById("img-view");

inputFile.addEventListener("change", handleFileSelect);
dropArea.addEventListener("dragover", handleDragOver);
dropArea.addEventListener("dragleave", handleDragLeave);
dropArea.addEventListener("drop", handleDrop);

function handleDragOver(e) {
    e.preventDefault();
    dropArea.classList.add("drag-over");
}

function handleDragLeave() {
    dropArea.classList.remove("drag-over");
}

function handleDrop(e) {
    e.preventDefault();
    dropArea.classList.remove("drag-over");
    const fileList = e.dataTransfer.files;
    if (fileList.length > 0) {
        inputFile.files = fileList;
        handleFileSelect();
    }
}
function handleFileSelect() {
    const input = document.getElementById("input-file");
    const fileList = input.files;

    if (fileList.length > 0) {
        const file = fileList[0];
        displayImage(file);
    }
}

function displayImage(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const imgLink = e.target.result;
        const imageView = document.getElementById("img-view");
        imageView.style.backgroundImage = `url(${imgLink})`;
        imageView.textContent = "";
        imageView.style.border = 0;
    };
    reader.readAsDataURL(file);
}

function renderImage(base64String) {
    var image = new Image();
    image.src = 'data:image/png;base64,' + base64String;
    document.body.appendChild(image);
}

window.onload = function () {
    renderImage("<?php echo $data['image']; ?>");
};