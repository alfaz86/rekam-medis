const updateTitleLogin = () => {
  const targetSelector = 'section header.fi-simple-header div.fi-logo';

  const tryUpdate = () => {
    const logo = document.querySelector(targetSelector);
    if (logo && window.location.pathname === '/panel/login') {
      logo.innerHTML = 'Praktik Bidan S. Rahayu Utami';
      observer.disconnect();
    }
  };

  const observer = new MutationObserver(() => {
    tryUpdate();
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });

  tryUpdate();
};

updateTitleLogin();
