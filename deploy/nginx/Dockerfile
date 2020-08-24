FROM nginx:stable-alpine

ARG BUILD_DATE="develop"
ARG VCS_REF="develop"

# The maintainer list
LABEL authors="Christoph Jaecks <christoph.jaecks@plista.com>"
LABEL org.label-schema.build-date=$BUILD_DATE
LABEL org.label-schema.vcs-ref=$VCS_REF

COPY deploy/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template
COPY deploy/nginx/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /www

COPY public /www/public

ENTRYPOINT ["/entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]
