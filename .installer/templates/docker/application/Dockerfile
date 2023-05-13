FROM webdevops/php-dev:{{phpVersion}}

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=phario/phive:0.15.2 /usr/local/bin/phive /usr/local/bin/phive

# configure services
RUN set -x \
    && apt-get -q update \
    && docker-service enable syslog \
    && docker-run-bootstrap \
    && docker-image-cleanup

WORKDIR /app

ARG UID
ARG GID

# Fix application uid/gid by given arguments
RUN echo "Change uid/gid of 'application' user (${UID}:${GID})" && \
    usermod -u ${UID} application && \
    groupmod -g ${GID} application && \
    usermod -g ${GID} application &&     \
    chown "$APPLICATION_USER":"$APPLICATION_GROUP" -R /app "/home/$APPLICATION_USER"
