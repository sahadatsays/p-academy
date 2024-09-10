import { deepMerge } from "@antfu/utils"
import { createVuetify } from "vuetify"
import * as components from "vuetify/components"
import * as directives from "vuetify/directives"

import { VBtn } from "vuetify/components/VBtn"
import defaults from "./defaults"
import { icons } from "./icons"
import { staticPrimaryColor, staticPrimaryDarkenColor, themes } from "./theme"
import { themeConfig } from "@themeConfig"
import { en, fr } from "vuetify/locale"

// Styles
import { cookieRef } from "@/@layouts/stores/config"
import "@core-scss/template/libs/vuetify/index.scss"
import "vuetify/styles"

export default function (app) {
  const cookieThemeValues = {
    defaultTheme: resolveVuetifyTheme(themeConfig.app.theme),
    themes: {
      light: {
        colors: {
          primary: cookieRef("lightThemePrimaryColor", staticPrimaryColor)
            .value,
          "primary-darken-1": cookieRef(
            "lightThemePrimaryDarkenColor",
            staticPrimaryDarkenColor,
          ).value,
        },
      },
      dark: {
        colors: {
          primary: cookieRef("darkThemePrimaryColor", staticPrimaryColor).value,
          "primary-darken-1": cookieRef(
            "darkThemePrimaryDarkenColor",
            staticPrimaryDarkenColor,
          ).value,
        },
      },
    },
  }

  const optionTheme = deepMerge({ themes }, cookieThemeValues)

  const vuetify = createVuetify({
    lang: {
      locales: { en, fr },
      current: "en",
    },
    aliases: {
      IconBtn: VBtn,
    },
    defaults,
    icons,
    theme: optionTheme,
    components,
    directives,
  })

  app.use(vuetify)
}
