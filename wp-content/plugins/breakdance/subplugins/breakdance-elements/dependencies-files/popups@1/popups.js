(function() {
  const { mergeObjects, matchMedia } = BreakdanceFrontend.utils;
  window.breakdancePopupInstances = {};
  window.breakdanceHasShownPopup = false;

  class BreakdancePopup {
    defaultOptions = {
      closeOnClickOutside: true,
      closeOnEscapeKey: true,
      closeAfterMilliseconds: null,
      showCloseButtonAfterMilliseconds: null,
      disableScrollWhenOpen: false,
      avoidMultiple: false,
      entranceAnimation: null,
      exitAnimation: null,
      limitPageLoad: null,
      limitSession: null,
      limitForever: null,
      breakpointConditions: [],
      triggers: [],
    };

    showCount = 0;
    idleTimeMilliseconds = 0;
    entranceAnimation = false;
    exitAnimation = false;
    lastScrollPosition = window.scrollY;

    boundEvents = {};

    openClass = "breakdance-popup-open";
    animatingClass = "breakdance-popup-animating";

    constructor(id, options) {
      this.id = id;
      this.options = mergeObjects(this.defaultOptions, options);
      this.triggers = this.options.triggers;
      this.storageKey = `breakdance_popup_${id}_count`;
      if (!window.breakdancePopupInstances[id]) {
        window.breakdancePopupInstances[id] = this;
      }
      this.template = document.querySelector(`template#popupTemplate${id}`);

      this.triggers.forEach(trigger => {
        this.initTrigger(trigger.slug, trigger.options);
      });
    }

    init() {
      if (this.template) {
        const popupContent = document.importNode(this.template.content, true);
        this.element = document.querySelector('body').appendChild(popupContent.querySelector('.bde-popup'));
        const popupInlineScripts = popupContent.querySelectorAll('script');
        [...popupInlineScripts].forEach((script) => {
          this.element.append(script);
        });
      } else {
        const isBuilder = false;// !!window.parent.Breakdance;
        if (isBuilder) {
          this.element = document.querySelector('.breakdance-popup');
        }
      }

      if (!this.element) {
        return false;
      }

      this.wrapper = this.element.closest('.bde-popup');

      if (!this.wrapper) {
        return false;
      }

      this.initAnimations();
      this.initCloseButton();
      // This is a temporary patch that should be removed
      // once we have handled applying JS on load
      // see https://github.com/soflyy/breakdance/issues/5513
      this.callJavaScriptAutoloaders();

      return true;
    }

    setOptions(options) {
      this.options = { ...this.options, ...options };
    }

    open(forceOpen = false) {
      return new Promise((resolve, reject) => {
        const initialised = this.init();
        if (!initialised) {
          return reject(`Unable to initialise popup ${this.id}`);
        }
        if (this.shouldHideAtBreakpoint()) {
          return resolve();
        }

        if (this.isOpen() || this.isAnimating()) {
          return resolve();
        }

        if (!forceOpen && this.hasReachedLimit()) {
          return resolve();
        }

        if (this.element.dataset?.breakdancePopupUsingProTriggers) {
          alert(
            `This popup is using a Pro-only trigger. The popup would show up now instead of this alert. Get Breakdance Pro to get access to all popup triggers.`
          );

          return resolve();
        }

        this.incrementShowCounts();

        this.registerCloseListeners();

        if (this.entranceAnimation) {
          this.entranceAnimation.play().then(() => {
            return resolve();
          });
        } else {
          this.wrapper.classList.add(this.openClass);
          return resolve();
        }
      });
    }

    registerCloseListeners() {
      this.boundCloseIfEscapeOrClickOutside = this.closeIfEscapeOrClickOutside.bind(
        this
      );

      if (this.options.closeOnEscapeKey) {
        document.addEventListener(
          "keydown",
          this.boundCloseIfEscapeOrClickOutside
        );
      }

      this.boundCloseOnAnchorLinkClick = this.closeOnAnchorLinkClick.bind(this);
      this.element.addEventListener("click", this.boundCloseOnAnchorLinkClick);

      if (this.options.closeOnClickOutside) {
        document.addEventListener(
          "mousedown",
          this.boundCloseIfEscapeOrClickOutside
        );
      }

      if (this.options.closeAfterMilliseconds) {
        setTimeout(() => {
          if (this.isOpen()) {
            this.close();
          }
        }, this.options.closeAfterMilliseconds);
      }

      if (this.options.disableScrollWhenOpen) {
        document.querySelector("html").classList.add("breakdance-noscroll");
      }
    }

    removeCloseListeners() {
      if (this.boundCloseIfEscapeOrClickOutside) {
        document.removeEventListener(
          "keydown",
          this.boundCloseIfEscapeOrClickOutside
        );
        document.removeEventListener(
          "mousedown",
          this.boundCloseIfEscapeOrClickOutside
        );
        this.element.removeEventListener(
          "click",
          this.boundCloseOnAnchorLinkClick
        );
      }
    }

    hasReachedLimit() {
      if (
        this.options.avoidMultiple &&
        window.breakdanceHasShownPopup === true
      ) {
        return true;
      }

      if (
        this.options.limitPageLoad &&
        this.getShowCount("page_load") >= this.options.limitPageLoad
      ) {
        return true;
      }

      if (
        this.options.limitSession &&
        this.getShowCount("session") >= this.options.limitSession
      ) {
        return true;
      }

      if (
        this.options.limitForever &&
        this.getShowCount("local") >= this.options.limitForever
      ) {
        return true;
      }

      return false;
    }

    isOpen() {
      return this.wrapper && this.wrapper.classList.contains(this.openClass);
    }

    isAnimating() {
      return this.wrapper && this.wrapper.classList.contains(this.animatingClass);
    }

    closeIfEscapeOrClickOutside(event) {
      const shouldClose =
        event.keyCode === 27 ||
        [...event.target.classList].includes("bde-popup");

      if (shouldClose) {
        event.preventDefault();
        event.stopPropagation();
        this.close();
      }
    }

    closeOnAnchorLinkClick(event) {
      if (event.target.matches("a[href^='#']")) {
        const targetId = event.target.getAttribute('href').substring(1);
        if (document.querySelectorAll(`#${targetId}, a[name=${targetId}]`)) {
          this.close();
        }
      }
    }

    close() {
      return new Promise((resolve, reject) => {
        if (!this.isOpen() || this.isAnimating()) {
          return resolve();
        }

        this.removeCloseListeners();

        if (this.options.disableScrollWhenOpen) {
          document
            .querySelector("html")
            .classList.remove("breakdance-noscroll");
        }

        if (this.exitAnimation) {
          this.exitAnimation.play(true).then(() => {
            document.querySelector("body").removeChild(this.element);
            this.wrapper = null;
            this.element = null;
            return resolve();
          });
        } else {
          document.querySelector("body").classList.remove(this.openClass);
          document.querySelector("body").removeChild(this.element);
          this.wrapper = null;
          this.element = null;
          return resolve();
        }
      });
    }

    toggle() {
      if (this.isOpen()) {
        return this.close();
      }
      return this.open();
    }

    showPopupOnScrollPercentage(options, callback) {
      return event => {
        const targetPercent = options.percent / 100;
        const scrollTop = window.scrollY;
        const documentHeight = document.body.offsetHeight;
        const windowHeight = window.innerHeight;
        const scrollPercent = scrollTop / (documentHeight - windowHeight);
        if (scrollPercent > targetPercent) {
          this.open().then(callback);
        }
      };
    }

    showPopupOnScrollToSelector(options) {
      const element = document.querySelector(options.selector);
      if (!element) {
        return;
      }
      const ob = new IntersectionObserver(
        (entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting && !this.isOpen()) {
              this.open().then(() => {
                observer.unobserve(entry.target);
              });
            }
          });
        },
        {
          root: null,
          rootMargin: "0px",
          threshold: 0.2
        }
      );
      ob.observe(element);
    }

    showPopupOnScrollUp(callback) {
      return event => {
        const scrollTop = window.scrollY;
        if (scrollTop < this.lastScrollPosition) {
          this.open().then(callback);
        }
        this.lastScrollPosition = scrollTop;
      };
    }

    showPopupAfterInactivityDelay(delayInMilliseconds) {
      const idleTimeout = 1000;
      const idleInterval = setInterval(() => {
        if (this.idleTimeMilliseconds >= delayInMilliseconds) {
          this.open();
          clearInterval(idleInterval);
          if (boundResetIdleTime) {
            document.removeEventListener("mousemove", boundResetIdleTime);
            document.removeEventListener("keydown", boundResetIdleTime);
          }
        } else {
          this.idleTimeMilliseconds += idleTimeout;
        }
      }, idleTimeout);
      const boundResetIdleTime = this.resetIdleTime.bind(this);
      document.addEventListener("mousemove", boundResetIdleTime);
      document.addEventListener("keydown", boundResetIdleTime);
    }

    resetIdleTime() {
      this.idleTimeMilliseconds = 0;
    }

    showPopupOnExitIntent(callback) {
      return event => {
        const isExitIntent =
          !event.toElement && !event.relatedTarget && event.clientY < 10;
        if (isExitIntent) {
          this.open().then(callback);
        }
      };
    }

    showPopupOnClickEvent(options, callback) {
      return event => {
        if (options.clickType === "anywhere") {
          return this.open().then(callback);
        }
        if (options.selector !== null) {
          if (event.target.closest(options.selector)) {
            event.preventDefault();
            event.stopPropagation();
            return this.open().then(callback);
          }
        }
      };
    }

    showPopupOnLoad(delayInMilliseconds) {
      setTimeout(() => {
        this.open();
      }, delayInMilliseconds);
    }

    initAnimations() {
      if (this.entranceAnimation) {
        this.entranceAnimation.destroy();
      }
      if (this.options.entranceAnimation !== null) {
        this.entranceAnimation = new BreakdancePopupAnimation(
          this.element,
          this.wrapper,
          this.options.entranceAnimation
        );
        if (this.exitAnimation) {
          this.exitAnimation.destroy();
        }
        if (this.options.exitAnimation !== null) {
          this.exitAnimation = new BreakdancePopupAnimation(
            this.element,
            this.wrapper,
            this.options.exitAnimation,
            true
          );
        }
      }
    }

    initTrigger(slug, options = {}) {
      if (slug === "load") {
        const delay = options.delay ? options.delay * 1000 : 0;
        this.showPopupOnLoad(delay);
      }

      if (slug === "inactivity") {
        const delay = options.delay ? options.delay * 1000 : 0;
        this.showPopupAfterInactivityDelay(delay);
      }

      if (slug === "scroll" && options.scrollType === "selector") {
        this.showPopupOnScrollToSelector(options);
      }

      if (slug === "scroll" && options.scrollType === "percent") {
        const scrollEventListener = this.showPopupOnScrollPercentage(
          options,
          () => {
            window.removeEventListener("scroll", scrollEventListener);
          }
        );
        window.addEventListener("scroll", scrollEventListener);
      }

      if (slug === "scroll_up") {
        const scrollUpEventListener = this.showPopupOnScrollUp(() => {
          window.removeEventListener("scroll", scrollUpEventListener);
        });
        window.addEventListener("scroll", scrollUpEventListener);
      }

      if (slug === "exit_intent") {
        const exitIntentEventListener = this.showPopupOnExitIntent(() => {
          document.removeEventListener("mouseout", exitIntentEventListener);
        });
        document.addEventListener("mouseout", exitIntentEventListener);
      }

      if (slug === "click") {
        const boundShowPopupOnClickEvent = this.showPopupOnClickEvent(
          options,
          () => {
            if (options.clickType === "anywhere") {
              document.removeEventListener("click", boundShowPopupOnClickEvent);
            }
          }
        );
        document.addEventListener("click", boundShowPopupOnClickEvent);
      }
    }

    initCloseButton() {
      if (this.options.showCloseButtonAfterMilliseconds) {
        setTimeout(() => {
          const closeButton = this.element.querySelector(
            ".breakdance-popup-close-button"
          );
          if (closeButton) {
            closeButton.classList.remove("hidden");
          }
        }, this.options.showCloseButtonAfterMilliseconds);
      }
    }

    incrementShowCounts() {
      window.breakdanceHasShownPopup = true;

      const sessionStorageValue = this.getShowCount("session");
      sessionStorage.setItem(
        this.storageKey,
        (sessionStorageValue + 1).toString()
      );

      const localStorageValue = this.getShowCount("local");
      localStorage.setItem(this.storageKey, (localStorageValue + 1).toString());

      this.showCount += 1;
    }

    getShowCount(type) {
      let showCount = 0;

      if (type === "page_load") {
        showCount = this.showCount;
      }

      if (type === "session") {
        const sessionStorageItem = sessionStorage.getItem(this.storageKey);
        showCount = sessionStorageItem ? parseInt(sessionStorageItem) : 0;
      }

      if (type === "local") {
        const localStorageItem = localStorage.getItem(this.storageKey);
        showCount = localStorageItem ? parseInt(localStorageItem) : 0;
      }

      return showCount;
    }

    shouldHideAtBreakpoint() {
      return this.options.breakpointConditions.some(condition => {
        const conditionApplies = condition.breakpoints.some(breakpointId => {
          return matchMedia(breakpointId);
        });

        if (condition.operand === "is one of") {
          return !conditionApplies;
        }

        if (condition.operand === "is none of") {
          return conditionApplies;
        }

        return false;
      });
    }

    static runAction(popupId, action = "open") {
      const isBuilder = !!window?.BreakdanceFrontend.utils.isBuilder();

      if (isBuilder) {
        return;
      }
      const breakdancePopupInstance = window.breakdancePopupInstances[popupId];
      if (
        breakdancePopupInstance &&
        typeof breakdancePopupInstance[action] === "function"
      ) {
        breakdancePopupInstance[action].call(breakdancePopupInstance, true);
      }
    }

    callJavaScriptAutoloaders() {
      if (window.BreakdanceLightbox) {
        window.BreakdanceLightbox.autoload(this.element);
      }
      if (window.BreakdanceLinkAction) {
        window.BreakdanceLinkAction.autoload(this.element);
      }
    }
  }

  window.BreakdancePopup = BreakdancePopup;
  document.addEventListener("click", event => {
    const popupTrigger = event.target.closest("[data-breakdance-popup-action]");
    if (popupTrigger) {
      const {
        breakdancePopupReference,
        breakdancePopupAction
      } = popupTrigger.dataset;
      if (breakdancePopupReference) {
        event.preventDefault();
        event.stopPropagation();
        BreakdancePopup.runAction(
          breakdancePopupReference,
          breakdancePopupAction
        );
      }
    }
  });
})();
