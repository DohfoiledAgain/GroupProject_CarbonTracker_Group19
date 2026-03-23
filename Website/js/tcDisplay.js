// terms and conditions
const tcCheckbox = document.getElementById("tc-accept-cb");
const tcButton = document.getElementById("tc-accept-btn");
const tcOverlay = document.getElementById("tc-overlay");

console.log(tcCheckbox);
console.log(tcButton);
console.log(tcOverlay);

if (localStorage.getItem("tcAccepted") !== "true") {
  tcOverlay.style.display = "flex";
}

tcCheckbox.addEventListener("change", function() {
    tcButton.disabled = !tcCheckbox.checked;
});

tcButton.addEventListener("click", function(){
  tcOverlay.style.display = "none";
  localStorage.setItem("tcAccepted", "true");
});