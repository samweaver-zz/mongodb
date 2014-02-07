from senti_classifier import senti_classifier
sentences = ['Hahahha explaining to my mum why the apple appstore needs a card altho the app is free, is just hilarious']
pos_score, neg_score = senti_classifier.polarity_scores(sentences)
print pos_score, neg_score
