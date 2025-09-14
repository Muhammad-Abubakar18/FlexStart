document.getElementById("experience-form").addEventListener("submit", function (e) {
  e.preventDefault();

  // You can store data to localStorage or pass it to the next step as needed
  // For now, we just move to the next step
  window.location.href = "step4-skills.html";
});

// STEP 4: Skills
document.addEventListener("DOMContentLoaded", () => {
  const skillsList = document.getElementById("skillsList");

  if (skillsList) {
    let skillCount = skillsList.children.length;

    window.addSkillField = function () {
      skillCount++;
      const newField = document.createElement("div");
      newField.classList.add("form-group");

      const label = document.createElement("label");
      label.setAttribute("for", `skill${skillCount}`);
      label.innerText = `Skill ${skillCount}`;

      const input = document.createElement("input");
      input.type = "text";
      input.name = "skills[]";
      input.id = `skill${skillCount}`;
      input.placeholder = "Enter a skill";
      input.required = true;

      newField.appendChild(label);
      newField.appendChild(input);
      skillsList.appendChild(newField);
    };
  }
});
// STEP 5: Summary
document.addEventListener("DOMContentLoaded", () => {
  const summaryForm = document.getElementById("summaryForm");

  if (summaryForm) {
    summaryForm.addEventListener("submit", function (e) {
      const summary = document.getElementById("summary").value.trim();
      if (summary === "") {
        e.preventDefault();
        alert("Please write a professional summary before continuing.");
      }
    });
  }
});
// STEP 6: Review
document.addEventListener("DOMContentLoaded", () => {
  const reviewContent = document.getElementById("reviewContent");

  if (reviewContent) {
    const personal = JSON.parse(localStorage.getItem("personal")) || {};
    const education = JSON.parse(localStorage.getItem("education")) || {};
    const experience = JSON.parse(localStorage.getItem("experience")) || {};
    const skills = JSON.parse(localStorage.getItem("skills")) || [];
    const summary = localStorage.getItem("summary") || "";

    reviewContent.innerHTML = `
      <h3>Personal Information</h3>
      <p><strong>Full Name:</strong> ${personal.fullname || ""}</p>
      <p><strong>Email:</strong> ${personal.email || ""}</p>
      <p><strong>Phone:</strong> ${personal.phone || ""}</p>
      <p><strong>Address:</strong> ${personal.address || ""}</p>

      <h3>Education</h3>
      <p><strong>Degree:</strong> ${education.degree || ""}</p>
      <p><strong>Institution:</strong> ${education.institution || ""}</p>
      <p><strong>Year:</strong> ${education.year || ""}</p>

      <h3>Experience</h3>
      <p><strong>Job Title:</strong> ${experience.title || ""}</p>
      <p><strong>Company:</strong> ${experience.company || ""}</p>
      <p><strong>Duration:</strong> ${experience.duration || ""}</p>

      <h3>Skills</h3>
      <ul>${skills.map(skill => `<li>${skill}</li>`).join("")}</ul>

      <h3>Summary</h3>
      <p>${summary}</p>
    `;
  }
});

// STEP 7: Populate Resume Preview and Enable PDF Download
document.addEventListener("DOMContentLoaded", () => {
  const previewDiv = document.getElementById("resumePreview");

  if (previewDiv) {
    const personal = JSON.parse(localStorage.getItem("personal")) || {};
    const education = JSON.parse(localStorage.getItem("education")) || {};
    const experience = JSON.parse(localStorage.getItem("experience")) || {};
    const skills = JSON.parse(localStorage.getItem("skills")) || [];
    const summary = localStorage.getItem("summary") || "";

    previewDiv.innerHTML = `
      <div style="padding: 20px; background: #fff; border-radius: 10px;">
        <h1 style="color:#00b074;">${personal.fullname || "Your Name"}</h1>
        <p>${personal.email || ""} | ${personal.phone || ""} | ${personal.address || ""}</p>
        <hr style="margin: 20px 0; border-color: #00b074;">

        <h2 style="color:#00b074;">Summary</h2>
        <p>${summary}</p>

        <h2 style="color:#00b074;">Education</h2>
        <p><strong>${education.degree}</strong>, ${education.institution} (${education.year})</p>

        <h2 style="color:#00b074;">Experience</h2>
        <p><strong>${experience.title}</strong> at ${experience.company} (${experience.duration})</p>

        <h2 style="color:#00b074;">Skills</h2>
        <ul>${skills.map(skill => `<li>${skill}</li>`).join("")}</ul>
      </div>
    `;
  }
});

function downloadResume() {
  const element = document.getElementById("resumePreview");
  const opt = {
    margin:       0.5,
    filename:     'my_resume.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2 },
    jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
  };
  html2pdf().set(opt).from(element).save();
}
