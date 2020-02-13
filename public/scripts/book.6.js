document.addEventListener("DOMContentLoaded", () => {
  const selectWhen = document.getElementById("select-when");
  const selectWho = document.getElementById("select-who");

  // when cinema chosen
  document.getElementById("select-where").addEventListener("change", e => {
    selectWhen.style.display = ""; // reveal

    // clear current times except placeholder
    const screeningOpts = document.getElementById("screening-options");
    const options = screeningOpts.children;

    // leave 'choose time' placeholder
    for (let i = 1; i < options.length; ++i) {
      screeningOpts.removeChild(options[i]);
    }

    // get screenings for cinema
    const screenings = JSON.parse(
      e.target.selectedOptions[0].getAttribute("data-screenings")
    );

    // add options
    for (const screening of screenings) {
      // create elements
      const opt = document.createElement("option");
      opt.setAttribute("value", screening.id);
      opt.textContent = screening.start;

      screeningOpts.appendChild(opt);
    }
  });

  // when time chosen
  selectWhen.addEventListener("change", () => {
    selectWho.style.display = "";
  });
});
