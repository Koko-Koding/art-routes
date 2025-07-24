# Dockerfile for compiling .po to .mo using gettext tools
FROM ubuntu:22.04

RUN apt-get update && \
    apt-get install -y gettext && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Usage:
# docker build -t po-compiler .
# docker run --rm -v ${PWD}/languages:/app po-compiler msgfmt wp-art-routes-nl_NL.po -o wp-art-routes-nl_NL.mo
