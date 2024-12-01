function updateContentField() {
    const dropdown = document.getElementById("content-type");
    const contentLabel = document.querySelector("label[for='content']");
    const contentField = document.getElementById("content");

    if (dropdown.value === "video") {
        contentLabel.textContent = "Input link to video:";
        contentField.outerHTML = `<input type="text" id="content" name="content" required>`;
    } else if (dropdown.value === "text") {
        contentLabel.textContent = "Text content of the post:";
        contentField.outerHTML = `<input type="text" id="content" name="content" required>`;
    } 
    else if (dropdown.value === "image") {
        contentLabel.textContent = "Input link to image:";
        contentField.outerHTML = `<input type="text" id="content" name="content" required>`;
    }
}