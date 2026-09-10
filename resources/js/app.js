const appMenuToggle = document.querySelector('[data-menu-toggle]');
const appSidebar = document.querySelector('[data-sidebar]');
const saprodiCategorySelect = document.querySelector('[data-saprodi-category]');
const customCategoryField = document.querySelector('[data-custom-category-field]');
const customCategoryInput = document.querySelector('[data-custom-category-input]');

const syncCustomCategoryField = () => {
    const isCustomCategory = saprodiCategorySelect?.value === 'Lainnya';

    customCategoryField?.toggleAttribute('hidden', ! isCustomCategory);
    customCategoryInput?.toggleAttribute('required', isCustomCategory);
};

saprodiCategorySelect?.addEventListener('change', syncCustomCategoryField);
syncCustomCategoryField();

const closeAppSidebar = () => {
    appSidebar?.classList.remove('open');
    document.body.classList.remove('app-sidebar-open');
    appMenuToggle?.setAttribute('aria-expanded', 'false');
};

appMenuToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    const isOpen = appSidebar?.classList.toggle('open') ?? false;
    document.body.classList.toggle('app-sidebar-open', isOpen);
    appMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
});

document.addEventListener('click', (event) => {
    if (! appSidebar?.classList.contains('open') || event.target.closest('[data-sidebar]')) {
        return;
    }

    closeAppSidebar();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && appSidebar?.classList.contains('open')) {
        closeAppSidebar();
        appMenuToggle?.focus();
    }
});
const landingMenuToggle = document.querySelector('[data-landing-menu-toggle]');
const landingNav = document.querySelector('#landing-menu');
const landingShell = document.querySelector('.landing-shell');
const landingMenuClose = document.querySelector('[data-landing-menu-close]');
const landingMenuBackdrop = document.querySelector('[data-landing-menu-backdrop]');

document.querySelectorAll('[data-auto-dismiss]').forEach((notice) => {
    const duration = Number(notice.dataset.autoDismiss) || 2600;

    window.setTimeout(() => {
        notice.classList.add('is-leaving');
        window.setTimeout(() => notice.remove(), 220);
    }, duration);
});

landingMenuToggle?.addEventListener('click', () => {
    const isOpen = landingNav?.classList.toggle('is-open') ?? false;
    landingMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    landingShell?.classList.toggle('menu-open', isOpen);
    document.body.classList.toggle('landing-menu-open', isOpen);
});

const closeLandingMenu = () => {
    landingNav?.classList.remove('is-open');
    landingShell?.classList.remove('menu-open');
    document.body.classList.remove('landing-menu-open');
    landingMenuToggle?.setAttribute('aria-expanded', 'false');
};

landingMenuClose?.addEventListener('click', closeLandingMenu);
landingMenuBackdrop?.addEventListener('click', closeLandingMenu);

landingNav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    closeLandingMenu();
}));

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || ! landingNav?.classList.contains('is-open')) {
        return;
    }

    closeLandingMenu();
});

const directoryMenuToggle = document.querySelector('[data-directory-menu-toggle]');
const directoryNav = document.querySelector('#directory-menu');

const closeDirectoryMenu = () => {
    directoryNav?.classList.remove('is-open');
    document.body.classList.remove('directory-menu-open');
    directoryMenuToggle?.setAttribute('aria-expanded', 'false');
};

directoryMenuToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    const isOpen = directoryNav?.classList.toggle('is-open') ?? false;
    directoryMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    document.body.classList.toggle('directory-menu-open', isOpen);
});

directoryNav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeDirectoryMenu));

document.addEventListener('click', (event) => {
    if (! directoryNav?.classList.contains('is-open') || event.target.closest('#directory-menu')) {
        return;
    }

    closeDirectoryMenu();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && directoryNav?.classList.contains('is-open')) {
        closeDirectoryMenu();
        directoryMenuToggle?.focus();
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 1024 && directoryNav?.classList.contains('is-open')) {
        closeDirectoryMenu();
    }
});
const confirmationModal = document.createElement('div');
confirmationModal.className = 'confirmation-modal';
confirmationModal.hidden = true;
confirmationModal.setAttribute('aria-hidden', 'true');
confirmationModal.innerHTML = `
    <button class="confirmation-modal__backdrop" type="button" data-confirmation-dismiss aria-label="Tutup konfirmasi"></button>
    <section class="confirmation-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="confirmation-title" aria-describedby="confirmation-message" tabindex="-1">
        <button class="confirmation-modal__close" type="button" data-confirmation-dismiss aria-label="Tutup">×</button>
        <span class="confirmation-modal__icon" aria-hidden="true">!</span>
        <p class="confirmation-modal__eyebrow">KONFIRMASI</p>
        <h2 id="confirmation-title">Lanjutkan tindakan?</h2>
        <p id="confirmation-message"></p>
        <div class="confirmation-modal__actions">
            <button class="confirmation-modal__cancel" type="button" data-confirmation-dismiss>Batal</button>
            <button class="confirmation-modal__confirm" type="button" data-confirmation-approve>Ya, lanjutkan</button>
        </div>
    </section>
`;
document.body.append(confirmationModal);

const confirmationDialog = confirmationModal.querySelector('.confirmation-modal__dialog');
const confirmationTitle = confirmationModal.querySelector('#confirmation-title');
const confirmationMessage = confirmationModal.querySelector('#confirmation-message');
const confirmationApprove = confirmationModal.querySelector('[data-confirmation-approve]');
let pendingConfirmationForm = null;
let pendingConfirmationSubmitter = null;

const closeConfirmationModal = () => {
    if (confirmationModal.hidden) {
        return;
    }

    confirmationModal.classList.remove('is-visible');
    confirmationModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    window.setTimeout(() => {
        confirmationModal.hidden = true;
    }, 180);
    pendingConfirmationSubmitter?.focus();
    pendingConfirmationForm = null;
    pendingConfirmationSubmitter = null;
};

const openConfirmationModal = (form, submitter) => {
    const isDeleteAction = form.querySelector('input[name="_method"][value="delete" i]') !== null;

    pendingConfirmationForm = form;
    pendingConfirmationSubmitter = submitter;
    confirmationModal.classList.toggle('confirmation-modal--danger', isDeleteAction);
    confirmationTitle.textContent = isDeleteAction ? 'Hapus data ini?' : 'Konfirmasi tindakan';
    confirmationMessage.textContent = form.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan?';
    confirmationApprove.textContent = isDeleteAction ? 'Ya, hapus' : 'Ya, lanjutkan';
    confirmationModal.hidden = false;
    confirmationModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    window.requestAnimationFrame(() => {
        confirmationModal.classList.add('is-visible');
        confirmationDialog?.focus();
    });
};

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (! (form instanceof HTMLFormElement) || ! form.dataset.confirm) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;

        return;
    }

    event.preventDefault();
    openConfirmationModal(form, event.submitter);
});

confirmationModal.querySelectorAll('[data-confirmation-dismiss]').forEach((element) => element.addEventListener('click', closeConfirmationModal));
confirmationApprove?.addEventListener('click', () => {
    if (! pendingConfirmationForm) {
        return;
    }

    const form = pendingConfirmationForm;
    const submitter = pendingConfirmationSubmitter;
    confirmationModal.hidden = true;
    confirmationModal.classList.remove('is-visible');
    confirmationModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    pendingConfirmationForm = null;
    pendingConfirmationSubmitter = null;
    form.dataset.confirmed = 'true';
    form.requestSubmit(submitter instanceof HTMLElement ? submitter : undefined);
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && ! confirmationModal.hidden) {
        closeConfirmationModal();
    }
});

const securityUser = document.querySelector('[data-security-user]');
const currentPasswordField = document.querySelector('[data-current-password-field]');
const currentPasswordInput = document.querySelector('#current_password');

const updateSecurityForm = () => {
    const isCurrentUser = securityUser?.selectedOptions[0]?.dataset.isCurrent === 'true';

    if (! currentPasswordField || ! currentPasswordInput) {
        return;
    }

    currentPasswordField.hidden = ! isCurrentUser;
    currentPasswordInput.required = isCurrentUser;
    if (! isCurrentUser) {
        currentPasswordInput.value = '';
    }
};

securityUser?.addEventListener('change', updateSecurityForm);
updateSecurityForm();

const logoutModal = document.querySelector('[data-logout-modal]');
const logoutDialog = document.querySelector('[data-logout-dialog]');
let logoutTrigger = null;

const closeLogoutModal = () => {
    if (! logoutModal) {
        return;
    }

    logoutModal.hidden = true;
    logoutModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    logoutTrigger?.focus();
};

document.querySelector('[data-logout-trigger]')?.addEventListener('click', (event) => {
    if (! logoutModal) {
        return;
    }

    logoutTrigger = event.currentTarget;
    logoutModal.hidden = false;
    logoutModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    logoutDialog?.focus();
});

document.querySelectorAll('[data-logout-dismiss]').forEach((element) => element.addEventListener('click', closeLogoutModal));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && ! logoutModal?.hidden) {
        closeLogoutModal();
    }
});

const pageLoader = document.querySelector('[data-page-loader]');
let loaderHideTimer = null;
let loaderFallbackTimer = null;

const hidePageLoader = () => {
    if (! pageLoader) {
        return;
    }

    window.clearTimeout(loaderFallbackTimer);
    pageLoader.classList.remove('is-visible');
    pageLoader.setAttribute('aria-hidden', 'true');
    loaderHideTimer = window.setTimeout(() => {
        pageLoader.hidden = true;
    }, 320);
};

const showPageLoader = () => {
    if (! pageLoader) {
        return;
    }

    window.clearTimeout(loaderHideTimer);
    window.clearTimeout(loaderFallbackTimer);
    pageLoader.hidden = false;
    pageLoader.setAttribute('aria-hidden', 'false');
    window.requestAnimationFrame(() => pageLoader.classList.add('is-visible'));
    loaderFallbackTimer = window.setTimeout(hidePageLoader, 5000);
};

const showEntryLoader = () => {
    showPageLoader();
    window.setTimeout(hidePageLoader, 900);
};

const loaderReason = document.body.dataset.loaderReason;
const initialLoaderStorageKey = 'simantap.initial-loader-shown';

if (loaderReason === 'initial' && ! window.sessionStorage.getItem(initialLoaderStorageKey)) {
    window.sessionStorage.setItem(initialLoaderStorageKey, 'true');
    showEntryLoader();
} else if (loaderReason === 'login' || loaderReason === 'logout') {
    showEntryLoader();
}
