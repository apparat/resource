# Apparat Resources
[![Build Status](https://img.shields.io/travis/apparat/resource.svg)](https://travis-ci.org/apparat/resource)
[![Code Coverage](https://img.shields.io/coveralls/apparat/ApparatResource.svg)](https://coveralls.io/r/apparat/ApparatResource)

# File types planned to be supported

* [ ] Generic
* [ ] Text
* [ ] Markdown (CommonMark)
* [ ] YAML
* [ ] JSON
* [ ] YFM-Markdown (Markdown with YAML front matter)
* [ ] JFM-Markdown (Markdown with JSON front matter)

* [ ] MIME Messages

Each file consists of one or more **file parts**. The content model of a file is described in terms of

* **body parts** (containing true file content like text or image data) and / or
* **container parts** (each consisting of one or more subparts).

A container part may either be a

* **subpart sequence** (a predefined sequence of subparts of particular types) or a
* **subpart choice** (one of several allowed subpart types)

and may be repeated more than once.