class BondCookieBar {
  constructor () {
    this._bar = document.createElement('div')
    this._bar.classList.add('bond-cc-bar')
    this._settings = window.bond_cc_data

    this._cookieName = 'bond-cc-cookie'
    this._cookieLifespan = 60 * 60 * 24 * 365 // One year in seconds

    this.destroy = this.destroy.bind(this)
    this.setCookie = this.setCookie.bind(this)
    this.addDismissButton = this.addDismissButton.bind(this)
    this.onCookieBtnClick = this.onCookieBtnClick.bind(this)

    this.addInfoText()
    this.setBarStyles()
    this.setBarPosition()
    this.addDismissButton(this.onCookieBtnClick)
  }

  setBarStyles () {
    this._bar.style.backgroundColor = this._settings.bg_color
    this._bar.style.color = this._settings.info_text_color
    this._bar.style.padding = '6px'
    this._bar.style.textAlign = 'center'
  }

  setBarPosition () {
    if (this._settings.position === 'top') {
      this._bar.style.top = 0
    } else {
      this._bar.style.bottom = 0
    }
    this._bar.style.width = '100%'
    this._bar.style.position = 'fixed'
    this._bar.style.zIndex = 999
  }

  addInfoText () {
    // Create paragraph for the info text.
    var infoTextEl = document.createElement('p')
    infoTextEl.classList.add('bond-cc-bar__info')
    infoTextEl.style.marginBottom = '6px'
    infoTextEl.style.display = 'inline-block'
    infoTextEl.textContent = this._settings.info_text + ' '

    // Add possible link.
    if (this._settings.link_href) {
      var linkEl = document.createElement('a')
      linkEl.textContent = this._settings.link_text
      linkEl.href = this._settings.link_href
      linkEl.style.color = this._settings.link_text_color
      infoTextEl.appendChild(linkEl)
    }
    this._bar.appendChild(infoTextEl)
  }

  addDismissButton (fn) {
    var button = document.createElement('input')
    button.classList.add('bond-cc-bar__btn')
    button.type = 'button'
    button.value = this._settings.dismiss_text
    button.style.backgroundColor = this._settings.button_bg_color
    button.style.color = this._settings.button_text_color
    button.style.border = 'none'
    button.style.cursor = 'pointer'
    button.style.marginLeft = '12px'
    button.style.marginRight = '12px'
    button.style.paddingTop = '6px'
    button.style.paddingBottom = '6px'
    button.style.paddingLeft = '12px'
    button.style.paddingRight = '12px'

    button.onclick = fn

    this._bar.appendChild(button)
  }

  destroy () {
    this._bar.parentNode.removeChild(this._bar)
  }

  shouldShow () {
    return !this.hasCookie()
  }

  onCookieBtnClick () {
    this.setCookie()
    this.destroy()
  }

  setCookie () {
    var today = new Date()
    today.setSeconds(today.getSeconds() + this._cookieLifespan)
    var todayUTCStr = today.toUTCString()
    document.cookie = `${this._cookieName}=1;max-age=${this._cookieLifespan};expires=${todayUTCStr}`
  }

  hasCookie () {
    return document.cookie.indexOf(this._cookieName) !== -1
  }

  init () {
    if (this.shouldShow()) {
      document.body.appendChild(this._bar)
    }
  }
}
var bar = new BondCookieBar()
bar.init()
