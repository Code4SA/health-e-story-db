# health-e Story DB

## Installation

Requirements:

- Pods

Install:

1. Install and activate this plugin (Health-e Story DB)

## Import historic data

Requirements:

- Health-e Story DB
- WP All Import
- WP All Export
- WP REST API v1
- Pods JSON API
- Pods Deploy

Import:

1. Import pods using (Pods Deploy)[http://pods.io/tutorials/pods-deployment-best-practices/] according to best practises

 - Failing that, by pasting pods.json into the Pods migration import page and manually setting up bi-directional relationships again (pods packages don't set those up automatically)

2. Import Outlets by importing outlet CSVs via the WP All Import plugin
3. Export outlet titles and IDs to CSV via WP All Export plugin
3. Import Syndications using the Health-e Story DB export/import page under Tools
