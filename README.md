# Apparat Resources
[![Build Status](https://secure.travis-ci.org/apparat/resource.svg)](https://travis-ci.org/apparat/resource)
[![Coverage Status](https://coveralls.io/repos/apparat/resource/badge.svg?branch=ddd&service=github)](https://coveralls.io/github/apparat/resource?branch=ddd)

Apparat resource abstraction layer

# Purpose of this module

* Provide an abstract interface for basic file operations
	* Creating
	* Reading
	* Writing / Updating
	* Deleting
	* Copying
* Provide an easy-to-use interface for multipart files (e.g. files with YAML front matter)
	* Support for arbitrary content models
		* Parsing
		* Serialisation
	* File part operations
		* Creating
		* Reading
		* Writing / Updating
		* Deleting
		* Copying
* Reading from / writing to different sources
	* File system
	* Standard input / output
	* FTP?
	* Remote storage?
* Implementing several file types and structures (see below)
	* Possibly special behaviour based on file type

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