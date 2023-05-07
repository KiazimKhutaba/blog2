#!/usr/bin/env bash

# this scripts updates /etc/hosts
# when creating local dev environment
# please, remove manually added to your system /etc/hosts entries
# as script doesn't this automatically
set -e


function create_hosts_entries {

    hub_template=""

    if [ "$1" == "de" ]; then
      hub_template="q3"
    else
      hub_template=$(printf "q3-%s" "$1")
    fi

    {
      printf "\n"
      printf "# =========== DApp Dev Domains ======================= #"
      printf "\n"
      printf "127.0.0.1\t %s.dapp.local\n"         "$hub_template"
      printf "127.0.0.1\t %s-backend.dapp.local\n" "$hub_template"
      printf "127.0.0.1\t %s-api.dapp.local\n"     "$hub_template"
      printf "# =========== added by scripts/update_hosts.sh ========== #"
      printf "\n"

    } >> /etc/hosts

    printf "/etc/hosts with %s hub updated!\n" "$1"
}


create_hosts_entries "de"
create_hosts_entries "uk"
create_hosts_entries "eu"

# read -p "Choose for which hubs you want create /etc/hosts entries: " -r choice

# case "$choice" in
#   de|DE ) create_hosts_entries "de";;
#   uk|UK ) create_hosts_entries "uk";;
#   eu|EU ) create_hosts_entries "eu";;
#   * ) echo "Unknown hub, choose from: de, uk, eu";;
# esac

