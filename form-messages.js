const query = new URLSearchParams(window.location.search);
const status = query.get("status");
const box = document.getElementById("message-box");

if (status === "success") {
  box.innerHTML = "<p style='color: green;'>🎉 Student registered successfully!</p>";
} else if (status === "email_exists") {
  box.innerHTML = "<p style='color: red;'>❌ This email is already registered.</p>";
} else if (status === "error") {
  box.innerHTML = "<p style='color: red;'>❌ Something went wrong. Please try again.</p>";
} else if (status === "invalid") {
  box.innerHTML = "<p style='color: red;'>❌ Invalid form submission.</p>";
}
