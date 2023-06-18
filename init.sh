#!/usr/bin/env bash

vendor/bin/phinx migrate -e development && vendor/bin/phinx seed:run