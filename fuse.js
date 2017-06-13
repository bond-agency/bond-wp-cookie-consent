const { FuseBox, BabelPlugin, UglifyJSPlugin } = require('fuse-box')

// Are we doing production build?
const production = process.env.NODE_ENV === 'production'

const fuse = FuseBox.init({
  homeDir: 'assets/src',
  output: 'assets/dist/$name.js',
  plugins: [
    BabelPlugin({
      presets: ['es2015']
    }),
    production && UglifyJSPlugin()
  ]
})

const bundle = fuse.bundle('bond-wp-cookie-consent.js')
  .instructions(`>bond-wp-cookie-consent.js`)
  .target('browser')

if (!production) {
  bundle.watch('*.js')
}

fuse.run()
