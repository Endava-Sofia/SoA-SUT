class ProfileCardElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        if (!this.initialized) {
            this.initialized = true;
            this.render();
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        this.setAttribute('tabindex', '0');
        
        // Keyboard navigation
        this.addEventListener('keydown', (e) => {
            switch(e.key) {
                case 'ArrowUp':
                    e.preventDefault();
                    this.moveCard('up');
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.moveCard('down');
                    break;
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    this.toggleFocus();
                    break;
            }
        });

        // Mouse interactions
        const card = this.shadowRoot.querySelector('.card');
        card.addEventListener('mouseenter', () => this.handleHover(true));
        card.addEventListener('mouseleave', () => this.handleHover(false));
    }

    handleHover(isHovering) {
        const card = this.shadowRoot.querySelector('.card');
        if (isHovering) {
            card.classList.add('hover');
            this.dispatchEvent(new CustomEvent('card-hover', { 
                detail: { hovering: true },
                bubbles: true,
                composed: true
            }));
        } else {
            card.classList.remove('hover');
            this.dispatchEvent(new CustomEvent('card-hover', { 
                detail: { hovering: false },
                bubbles: true,
                composed: true
            }));
        }
    }

    moveCard(direction) {
        const container = this.closest('.profile-container');
        if (!container) return;

        const cards = [...container.querySelectorAll('profile-card')];
        const currentIndex = cards.indexOf(this);
        
        if (direction === 'up' && currentIndex > 0) {
            container.insertBefore(this, cards[currentIndex - 1]);
            this.focus();
        } else if (direction === 'down' && currentIndex < cards.length - 1) {
            container.insertBefore(this, cards[currentIndex + 2]);
            this.focus();
        }
    }

    toggleFocus() {
        const card = this.shadowRoot.querySelector('.card');
        card.classList.toggle('focused');
        this.dispatchEvent(new CustomEvent('card-focus-toggle', { 
            detail: { focused: card.classList.contains('focused') },
            bubbles: true,
            composed: true
        }));
    }

    render() {
        const template = document.createElement('template');
        template.innerHTML = `
            <style>
                :host {
                    display: block;
                    margin: 1rem 0;
                }
                .card {
                    background: white;
                    border-radius: 15px;
                    padding: 2rem;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    position: relative;
                    overflow: hidden;
                }
                .card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    border-radius: 15px;
                    opacity: 0;
                    transition: opacity 0.3s;
                    pointer-events: none;
                }
                .card.hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
                }
                .card.hover::before {
                    opacity: 1;
                }
                .card.focused {
                    outline: none;
                    box-shadow: 0 0 0 3px rgba(13,110,253,0.25), 0 8px 24px rgba(0,0,0,0.15);
                }
                .card-content {
                    position: relative;
                    z-index: 1;
                }
                .profile-icon {
                    font-size: 4rem;
                    color: #0d6efd;
                    margin-bottom: 1rem;
                    position: relative;
                    display: inline-block;
                }
                .profile-icon::after {
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    top: 0;
                    left: 0;
                    background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, rgba(255,255,255,0) 70%);
                    border-radius: 50%;
                    z-index: -1;
                }
                .profile-info {
                    margin-top: 1.5rem;
                }
                .profile-info i {
                    margin-right: 0.5rem;
                    color: #6c757d;
                    transition: color 0.3s;
                }
                .card.hover .profile-info i {
                    color: #0d6efd;
                }
            </style>
            <div class="card">
                <div class="card-content">
                    <slot></slot>
                </div>
            </div>
        `;

        this.shadowRoot.appendChild(template.content.cloneNode(true));
    }
}

class ProfileContainerElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.render();
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.addEventListener('card-hover', (e) => {
            // Handle card hover events if needed
            console.log('Card hover:', e.detail);
        });

        this.addEventListener('card-focus-toggle', (e) => {
            // Handle card focus events if needed
            console.log('Card focus toggle:', e.detail);
        });
    }

    render() {
        const template = document.createElement('template');
        template.innerHTML = `
            <style>
                :host {
                    display: block;
                    padding: 1rem;
                }
                .container {
                    display: grid;
                    gap: 1rem;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                }
            </style>
            <div class="container">
                <slot></slot>
            </div>
        `;

        this.shadowRoot.appendChild(template.content.cloneNode(true));
    }
}

class ProfileEditElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        if (!this.initialized) {
            this.initialized = true;
            this.render();
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        const form = this.shadowRoot.querySelector('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                // Form submission is handled by PHP, so we don't prevent default
                this.dispatchEvent(new CustomEvent('profile-update-attempt', {
                    bubbles: true,
                    composed: true
                }));
            });
        }
    }

    render() {
        const style = `
            :host {
                display: block;
                width: 100%;
            }
            .edit-profile-form {
                padding: 20px;
            }
            .form-group {
                margin-bottom: 1rem;
            }
            label {
                font-weight: 500;
                margin-bottom: 0.5rem;
                display: block;
            }
            .form-control {
                width: 100%;
                padding: 0.375rem 0.75rem;
                font-size: 1rem;
                line-height: 1.5;
                border: 1px solid #ced4da;
                border-radius: 0.25rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            .form-control:focus {
                border-color: #86b7fe;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
            .btn-primary {
                color: #fff;
                background-color: #0d6efd;
                border-color: #0d6efd;
                padding: 0.375rem 0.75rem;
                border-radius: 0.25rem;
                cursor: pointer;
            }
            .btn-primary:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
            }
        `;

        // Get the original form content from light DOM
        const originalForm = this.innerHTML;

        this.shadowRoot.innerHTML = `
            <style>${style}</style>
            <div class="edit-profile-form">
                ${originalForm}
            </div>
        `;
    }
}

// Register custom elements
customElements.define('profile-card', ProfileCardElement);
customElements.define('profile-container', ProfileContainerElement);
customElements.define('profile-edit', ProfileEditElement);
