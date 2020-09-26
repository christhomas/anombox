#!/usr/bin/env sh

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "----------------------------------------------------------------------"
echo "env file creation: $(basename ${DIR})"
echo "----------------------------------------------------------------------"
for src in $(find ${DIR}/deploy/env/*.dist); do
  dst=$(echo "$src" | sed -e 's/\.dist//g')

  if [ ! -f "$dst" ]; then
    cp "$src" "$dst"
    echo "copied $src to $dst";
  else
    echo "$dst already exists"
  fi

done
echo "----------------------------------------------------------------------"
