# Art Routes Monorepo

This repository contains the Art Routes WordPress plugins:

| Plugin | Description | Directory |
|--------|-------------|-----------|
| **Art Routes** (Free) | Interactive art route maps with OpenStreetMap integration | [`plugins/art-routes/`](plugins/art-routes/) |
| **Art Routes Pro** | Premium add-on — QR codes, visitor analytics, PDF exports | [`plugins/art-routes-pro/`](plugins/art-routes-pro/) |

## Setup

```bash
git clone git@github.com:Koko-Koding/art-routes.git ~/repos/art-routes
cd ~/repos/art-routes
./bin/setup-dev    # Creates symlinks into Local Sites
```

## Build

```bash
./bin/build-free   # → build/art-routes-X.Y.Z.zip
./bin/build-pro    # → build/art-routes-pro-X.Y.Z.zip
```

## Other Commands

```bash
./bin/plugin-check            # Run WordPress Plugin Check on free plugin
./bin/translate                # Compile .po → .mo for all plugins
./bin/verify-editions-system   # Verify Editions system integration
./bin/validate-gpx             # Validate GPX export files
```

## Plugin READMEs

- [Art Routes (Free) README](plugins/art-routes/README.md)
