# apparat/resource

Purpose of this module:

* Provide an abstract low-level interface for basic file operations
	* [x] Creating
	* [x] Reading
	* [x] Writing / Updating
	* [x] Deleting
	* [x] Copying (= Reading + Writing with a different name)
	* [x] Moving / Renaming
* Provide an easy-to-use interface for multipart files (e.g. files with YAML front matter)
	* Support for arbitrary content models
		* [x] Parsing
		* [x] Serialisation
	* File part operations
		* [x] Creating
		* [x] Reading
		* [x] Writing / Updating
		* [ ] Deleting (?)
		* [ ] Copying (?)
* Reading from / writing to different sources
	* [x] In-Memory
	* [x] File system
	* [ ] Standard input / output (?)
	* [ ] FTP (?)
	* [ ] Remote storage (?)
* Implementing several file types and structures (see below)
	* Possibly special behaviour based on file type

# File types planned to be supported

* [x] Text
* [x] Markdown (CommonMark)
* [x] YAML
* [x] JSON
* [x] YFM-Markdown (Markdown with YAML front matter)
* [x] JFM-Markdown (Markdown with JSON front matter)

Each file consists of one or more **file parts**. The content model of a file is described in terms of

* **content parts** (containing true file content like text or image data) and / or
* **part aggregates** (each consisting of one or more subparts).

A part aggregage may either be a

* **part sequence** (a predefined sequence of subparts of particular types) or a
* **part choice** (one of several allowed subpart types)

and may be repeated more than once.

# Documentation

I recommend reading [the project documentation](http://apparat-resource.readthedocs.io/) on *Read the Docs*.

[![Documentation Status](https://readthedocs.org/projects/apparat-resource/badge/?version=latest)](http://apparat-resource.readthedocs.io/en/latest/?badge=latest)
