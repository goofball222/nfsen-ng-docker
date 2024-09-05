FROM alpine:edge

RUN apk update \
    && apk add bash \
    && apk add nfdump \
    && apk add procps \
    && apk add sfcapd \
    && apk add tzdata

WORKDIR /app
COPY entrypoint.sh .
RUN chmod a+x /app/entrypoint.sh

ENTRYPOINT [ "/bin/bash", "/app/entrypoint.sh" ]
