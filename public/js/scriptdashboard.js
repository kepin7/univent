// Navbar active link toggle
const navLinks = document.querySelectorAll('.nav-menu .nav-link');
navLinks.forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    navLinks.forEach(l => l.classList.remove('active'));
    link.classList.add('active');
  });
});
// Utility: format date and time nicely
function formatDateTime(dateTimeStr) {
  const optionsDate = { year: 'numeric', month: 'long', day: 'numeric' };
  const optionsTime = { hour: '2-digit', minute: '2-digit', hour12: false };
  const dt = new Date(dateTimeStr);
  if (isNaN(dt)) return "Invalid date";
  const date = dt.toLocaleDateString('en-US', optionsDate);
  const time = dt.toLocaleTimeString('en-US', optionsTime);
  return `${date} | ${time}`;
}

