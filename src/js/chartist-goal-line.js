/**
 * Chartist.js plugin to display a "target" or "goal" line across the chart.
 * Only tested with bar charts. Works for horizontal and vertical bars.
 *
 * Copyright (c) 2015 Yorkshire Interactive (yorkshireinteractive.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function(window, document, Chartist) {
  'use strict';

  var defaultOptions = {
    // The class name so you can style the text
    className: 'ct-goal-line',
    // The axis to draw the line. y == vertical bars, x == horizontal
    axis: 'y',
    // What value the goal line should be drawn at
    value: null
  };

  Chartist.plugins = Chartist.plugins || {};

  Chartist.plugins.ctGoalLine = function(options) {
    options = Chartist.extend({}, defaultOptions, options);
    return function ctGoalLine (chart) {

      chart.on('created', function(context) {

        var projectTarget = {
          y: function (chartRect, bounds, value) {
            var targetLineY = chartRect.y1 - (chartRect.height() / bounds.max * value);

            return {
              x1: chartRect.x1,
              x2: chartRect.x2,
              y1: targetLineY,
              y2: targetLineY
            }
          },
          x: function (chartRect, bounds, value) {
            var targetLineX = chartRect.x1 + (chartRect.width() / bounds.max * value);

            return {
              x1: targetLineX,
              x2: targetLineX,
              y1: chartRect.y1,
              y2: chartRect.y2
            }
          }
        };

        var targetLine = projectTarget[options.axis](context.chartRect, context.bounds, options.value)
        context.svg.elem('line', targetLine, options.className);
      });
    }
  }

}(window, document, Chartist));
