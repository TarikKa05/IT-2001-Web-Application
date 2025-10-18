function setTheme(theme) {
  document.body.classList.remove("light", "dark", "blue");
  document.body.classList.add(theme);
  localStorage.setItem("selectedTheme", theme);
}

document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("selectedTheme") || "light";
  setTheme(savedTheme);
});
