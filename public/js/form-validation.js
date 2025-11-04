// ===============================
// 🔍 MeFoodie Frontend Validations
// ===============================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  if (!form) return;

  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const phoneInput = document.getElementById("ph");
  const fssaiInput = document.getElementById("fssai");
  const stateInput = document.querySelector("[name='state']");
  const cityInput = document.querySelector("[name='city']");
  const districtInput = document.querySelector("[name='district']");
  const pincodeInput = document.querySelector("[name='pincode']");
  const websiteInput = document.getElementById("website");
  const tagsInput = document.getElementById("tags");

  // Utility: show error
  function showError(input, message) {
    let errorEl = input.nextElementSibling;
    if (!errorEl || !errorEl.classList.contains("error-msg")) {
      errorEl = document.createElement("small");
      errorEl.classList.add(
        "error-msg",
        "text-tomato",
        "text-sm",
        "font-medium",
        "mt-1",
        "block"
      );
      input.insertAdjacentElement("afterend", errorEl);
    }
    errorEl.textContent = message;
    input.classList.add("border-red-500");
  }

  // Utility: clear error
  function clearError(input) {
    let errorEl = input.nextElementSibling;
    if (errorEl && errorEl.classList.contains("error-msg")) {
      errorEl.textContent = "";
    }
    input.classList.remove("border-red-500");
  }

  // Utility: simple pattern check
  function isValidIndianMobile(num) {
    return (
      /^[6-9]\d{9}$/.test(num) &&
      !/(.)\1{9}/.test(num) &&
      !/1234567890|9876543210/.test(num)
    );
  }

  // Utility: website validation
  function fixAndValidateWebsite(url) {
    if (!/^https?:\/\//i.test(url)) url = "https://" + url;
    const domainPattern = /^https?:\/\/[a-zA-Z0-9.-]+\.[a-z]{2,}(\/.*)?$/;
    return domainPattern.test(url) ? url : null;
  }

  // ===============================
  // 🚀 Validate on Submit
  // ===============================
  form.addEventListener("submit", (e) => {
    let valid = true;

    // 🔹 Business Name
    clearError(nameInput);
    if (!/^[A-Za-z\s]{3,}$/.test(nameInput.value.trim())) {
      showError(
        nameInput,
        "Enter a valid business name (min 3 letters, no numbers or symbols)."
      );
      valid = false;
    }

    // 🔹 Email
    clearError(emailInput);
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
      showError(emailInput, "Enter a valid email address.");
      valid = false;
    }

    // 🔹 Phone
    clearError(phoneInput);
    if (!isValidIndianMobile(phoneInput.value.trim())) {
      showError(phoneInput, "Enter a valid 10-digit Indian mobile number.");
      valid = false;
    }

    // 🔹 FSSAI (optional but must be 14 digits if entered)
    clearError(fssaiInput);
    const fssaiVal = fssaiInput.value.trim();
    if (fssaiVal !== "" && !/^\d{14}$/.test(fssaiVal)) {
      showError(fssaiInput, "FSSAI number must contain exactly 14 digits.");
      valid = false;
    }

    // 🔹 Location Fields
    clearError(stateInput);
    clearError(cityInput);
    clearError(districtInput);
    clearError(pincodeInput);

    if (!stateInput.value.trim()) {
      showError(stateInput, "Select a state.");
      valid = false;
    }
    if (!cityInput.value.trim()) {
      showError(cityInput, "Enter a valid city.");
      valid = false;
    }
    if (!districtInput.value.trim()) {
      showError(districtInput, "Enter a valid district.");
      valid = false;
    }
    if (!/^\d{6}$/.test(pincodeInput.value.trim())) {
      showError(pincodeInput, "Enter a valid 6-digit pincode.");
      valid = false;
    }

    // 🔹 Website
    clearError(websiteInput);
    let site = websiteInput.value.trim();
    const validSite = fixAndValidateWebsite(site);
    if (!validSite) {
      showError(
        websiteInput,
        "Enter a valid website URL ending with .com, .in, etc."
      );
      valid = false;
    } else {
      websiteInput.value = validSite;
    }

    // 🔹 Tags
    clearError(tagsInput);
    const tags = tagsInput.value
      .split(",")
      .map((t) => t.trim())
      .filter(Boolean);
    if (tags.length === 0 || tags.length > 3) {
      showError(tagsInput, "Enter up to 3 tags separated by commas.");
      valid = false;
    }

    if (!valid) e.preventDefault(); // stop submission if invalid
  });
});
