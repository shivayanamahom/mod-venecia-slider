#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
version=$(sed -n 's:.*<version>\([^<]*\)</version>.*:\1:p' mod_dharma_slider.xml | head -n 1)
test -n "$version"
mkdir -p dist
archive="$PWD/dist/mod_dharma_slider-$version.zip"
test ! -e "$archive" || rm "$archive"
zip -qr "$archive" mod_dharma_slider.xml language media services src tmpl
shasum -a 256 "$archive"
