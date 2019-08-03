FROM php:7.3-cli-alpine

LABEL "com.github.actions.name"="psalm"
LABEL "com.github.actions.description"="Run psalm"
LABEL "com.github.actions.icon"="check-circle"
LABEL "com.github.actions.color"="green"

ADD entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
