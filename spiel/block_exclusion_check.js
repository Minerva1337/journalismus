
// Erlaubte Kombinationen prüfen
function getVerboteneKombinationen() {
  return window.blockExclusions || [];
}

function isVerboten(neuerId, bestehendeIds) {
  const verbote = getVerboteneKombinationen();
  return bestehendeIds.some(bestId =>
    verbote.some(pair =>
      (pair[0] == neuerId && pair[1] == bestId) || (pair[1] == neuerId && pair[0] == bestId)
    )
  );
}
