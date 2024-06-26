import { canNavigate } from "@layouts/plugins/casl"

export const setupGuards = router => {
  // 👉 router.beforeEach
  // Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards

  router.beforeEach(to => {
    /**
     * If it's a public route, continue navigation. This kind of pages are allowed to visited by login & non-login users. Basically, without any restrictions.
     */
    if (to.meta.public)
      return 

    /**
       * Check if user is logged in by checking if token & user data exists in local storage
       * Feel free to update this logic to suit your needs
     */
    const isLoggedIn = !!(useCookie('accessToken').value)
    
    console.log(isLoggedIn)

    if (to.meta.unauthenticatedOnly) {
      return '/'
    } else {
      return undefined
    }

    if (!canNavigate(to)) {
      /* eslint-disable indent */
      return isLoggedIn ? { name: 'not-authorized' }
        : {
          name: 'login',
              query: {
                  ...to.query,
                  to: to.fullPath !== '/' ? to.path : undefined,
              },
        }
        /* eslint-enable indent */
    }
  })
}
