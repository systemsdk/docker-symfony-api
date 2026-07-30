ARG REDIS_VERSION=8.6

FROM redis:${REDIS_VERSION}

COPY --chown=redis:redis redis.conf /usr/local/etc/redis/redis.conf
CMD [ "redis-server", "/usr/local/etc/redis/redis.conf" ]
