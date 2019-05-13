const
  path = require('path'),
  webpack = require('webpack'),
  MiniCssExtractPlugin = require('mini-css-extract-plugin'),
  CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
  mode: 'none',

  entry: {
    frontend: [ './scss/frontend.scss' ],
    backend: [ './scss/backend.scss' ]
  },

  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'pdf-light-viewer-[name].js'
  },

  externals: {
    jquery: 'jQuery'
  },

  plugins: [
    new webpack.WatchIgnorePlugin([
      'node_modules'
    ]),
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: 'pdf-light-viewer-[name].css',
      chunkFilename: '[id].css'
    })
  ],

  devtool: 'cheap-module-source-map',

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: [{
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
            plugins: ['@babel/plugin-transform-runtime']
          }
        }]
      },
      {
        test: /\.scss/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {}
          },
          { loader: 'css-loader' },
          { loader: 'resolve-url-loader' },
          { loader: 'sass-loader?sourceMap' }
        ]
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {}
          },
          'css-loader'
        ]
      },
      {
        test: /.*\.(gif|png|jpe?g)$/i,
        use: [{
          loader: 'file-loader',
          options: {
            hash: 'sha512',
            digest: 'hex'
          }
        }]
      },
      {
        test: /.*\.(woff|woff2|svg|ttf|eot)$/i,
        use: [{
          loader: 'file-loader',
          options: {
            hash: 'sha512',
            digest: 'hex'
          }
        }]
      }
    ]
  }
};
