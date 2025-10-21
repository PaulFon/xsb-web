#!/usr/bin/env bash
set -euo pipefail

# Deploy Mass static site to Lightsail (Bitnami Apache)
# Source: this repo's WWWROOT/mass_site/public-build/
# Target: /home/bitnami/htdocs/mass (DocumentRoot of mass.xsb.pub)

rsync -avz --delete \
  ./mass_site/public-build/ \
  xsb-lightsail:/home/bitnami/htdocs/mass/

# (Optional) Fix ownership/permissions on the server (safe to leave)
ssh xsb-lightsail '
  sudo chown -R bitnami:daemon /home/bitnami/htdocs/mass &&
  sudo find /home/bitnami/htdocs/mass -type d -exec sudo chmod 755 {} \; &&
  sudo find /home/bitnami/htdocs/mass -type f -exec sudo chmod 644 {} \;
'
