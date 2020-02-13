document.addEventListener("DOMContentLoaded", () => {
  const released = document.getElementById("released-picker");
  const picker = new Pikaday({
    field: released,
    // change form value on select
    onSelect: function() {
      released.setAttribute(
        "value",
        this.getDate()
          .toISOString()
          .substr(0, 10)
      );
    }
  });
});
