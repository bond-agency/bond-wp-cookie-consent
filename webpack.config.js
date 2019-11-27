const path = require( 'path' )
const webpack = require( 'webpack' )

// Webpack Plugins
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' )
const FriendlyErrorsPlugin = require( 'friendly-errors-webpack-plugin' )

// Custom configurations.
const mode = process.env.NODE_ENV == 'production' ? 'production' : 'development'
const dev = ( mode === 'development' )

/**
 * Configuration
 */
const config = {
  mode: mode,
  watch: dev,
  entry: {
    'bond-wp-cookie-consent': [ '@babel/polyfill', './assets/src/bond-wp-cookie-consent.js' ],
    admin: [ './assets/src/admin.js' ]
  },
  output: {
    filename: '[name].js',
    path: path.resolve( __dirname, 'assets/dist/' ),
  },
  module: {
    rules: [ {
      test: /\.(js|jsx|ts)$/,
      exclude: /node_modules/,
      use: [{
        loader: 'babel-loader',
      }]
    } ]
  },
  plugins: [
    new webpack.ProgressPlugin(),
    new webpack.NoEmitOnErrorsPlugin(),
    new FriendlyErrorsPlugin(),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [ '**/*', '!.gitkeep' ]
    })
  ],
}

module.exports = config