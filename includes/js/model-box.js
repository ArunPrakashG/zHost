var modal = document.getElementById("alert-model");
var span = document.getElementsByClassName("close")[0];
var modelText = document.getElementsByClassName("modal-text-content")[0];

function showAlertModel() {
  modal.style.display = "block";
  modelText.innerHTML = arguments.length > 0 ? arguments[0] : "Error occured. Please retry...";
}

span.onclick = function () {
  modal.style.display = "none";
};

window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
