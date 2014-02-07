from pymongo import MongoClient
from array import array
from senti_classifier import senti_classifier
from dateutil import parser

def escapeSpecialCharacters ( text, characters ):
    for character in characters:
        text = text.replace( character, '' )
    text = '"' + text + '"'
    text = text.replace("\n", "")
    return text

def getSentiment ( pos_score, neg_score ):
    if pos_score > neg_score:
        return "positive"
    elif neg_score > pos_score:
        return "negative"
    elif pos_score == neg_score:
        return "neutral"

def convertDateTime(dt):
    return parser.parse(dt)

client = MongoClient()
db = client.blackhole
tweets_c = db.singularity
sentiment_c = db.sentiment

for post in tweets_c.find({},{'_id':0, 'text':1, 'created_at':1, 'user.location':1}):
    
    dateTime = convertDateTime(post['created_at'])
    text = escapeSpecialCharacters( post['text'], '\'"/\\' )
    pos_score, neg_score = senti_classifier.polarity_scores([text])
    sentiment = getSentiment(pos_score, neg_score)

    doc = { "queryable": "APPLE",
            "pos_score": pos_score,
            "neg_score": neg_score,
            "sentiment": sentiment,
            "dateTime": dateTime }  
    
    sentiment_c.insert(doc)

    print doc