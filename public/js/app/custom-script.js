const updateTitleLogin = () => {
  const targetSelector = 'section header.fi-simple-header div.fi-logo';

  const tryUpdate = () => {
    const logo = document.querySelector(targetSelector);
    if (logo && window.location.pathname === '/panel/login') {
      logo.innerHTML = 'Praktik Bidan S. Rahayu Utami';
      observer.disconnect(); // stop observing setelah berhasil
    }
  };

  const observer = new MutationObserver(() => {
    tryUpdate();
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });

  // Coba langsung juga saat awal, siapa tahu udah ada
  tryUpdate();
};

updateTitleLogin();
