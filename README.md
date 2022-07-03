# Magpie PHP Developer Challenge

Project addressing [Magpie php webscrapper devloper challenge](https://github.com/stickeeuk/magpie-developer-challenge)

### Setting Up

This project heavily relies on composer to run tests as well as run the scrapper task. Installing entails

1. `git clone https://github.com/richard-muvirimi/magpie-developer-challenge && cd magpie-developer-challenge`
2. `echo "install dependencies" && composer install`
3. `echo "run code sniffer and tests" && composer qc`
4. `echo "scrape website" && composer scrape`

You can still initiate the scrapper by visiting project folder and  `index.php`

### Tools Used

1. PHP text analysis
  - Uses NLP (Natural Language Processing) to determine and extract relavant data from the website. 
  - Training models are stored in the training folder for easy maintanance, and are loaded on each run
2. Unit Tests
  - Uses PhpUmit to test different units of the project, to ensure the desired result is obtained during maintanance
  - To run `composer test` though can also be triggered through `composer qc`
3. Miscellaneous Tools
  - Lodash-php: For array iteration the javascript way
  - Url-resolver: To resolve relative urls
  - byte: For converting between storage units
  - Codesniffer and PHP-compatibility: For discovring bugs during devlopment

### Outputs

1. No terminal or web output is produced except for an `output.json` file that is updated on each run.

### License

```
MIT License

Copyright (c) 2022 Richard Muvirimi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
