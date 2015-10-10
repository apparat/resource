# Apparat Resources
[![Build Status](https://secure.travis-ci.org/apparat/resource.svg)](https://travis-ci.org/apparat/resource)
[![Coverage Status](https://coveralls.io/repos/apparat/resource/badge.svg?branch=master&service=github)](https://coveralls.io/github/apparat/resource?branch=master)

Apparat resource abstraction layer

# File types planned to be supported

* [x] Generic
* [x] Text
* [x] Markdown (CommonMark)
* [x] YAML
* [ ] JSON
* [x] YFM-Markdown (Markdown with YAML front matter)
* [ ] JFM-Markdown (Markdown with JSON front matter)

* [ ] MIME Messages

Each file consists of one or more **file parts**. The content model of a file is described in terms of

* **body parts** (containing true file content like text or image data) and / or
* **container parts** (each consisting of one or more subparts).

A container part may either be a

* **subpart sequence** (a predefined sequence of subparts of particular types) or a
* **subpart choice** (one of several allowed subpart types)

and may be repeated more than once.