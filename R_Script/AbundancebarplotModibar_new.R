# Bar plot for number of samples in 4-10 samples
#rm(list=ls())
args <- commandArgs(TRUE)
library(reshape2)
library(ggplot2)
library(scales)
# library(randomcoloR)

data=read.table(args[1], sep = "\t", header=T)
head(data)
data1=as.data.frame(t(data[,2:ncol(data)]))
colnames(data1)=data$taxonomy
data2=cbind(taxonomy=row.names(data1),data1)
data3=melt(data2,id.var="taxonomy")

#http://tools.medialab.sciences-po.fr/iwanthue/index.php
palette<-c("#f36c80",
           "#a5ae31",
           "#ad93c4",
           "#eb9827",
           "#a9cbeb",
           "#9cd3d9",
           "#54addc",
           "#bec079",
           "green3", #"#b486e9",
           "#e9c58f",
           "#b998e6",
           "#f1745d",
           "#dda09c",
           "red", # "#c093a6",
           "#bede6f",
           "#9a6cf0",
           "#ccc0e9",
           "#f257de",
           "#d6ef6a",
           "#e5b3e9",
           "#59b08a",
           "#d08939",
           "#e9d02e",
           "#d07fdd",
           "#4bca7f",
           "#b0edd9",
           "#59ed94",
           "#9fddad",
           "#81a5c1",
           "#67c02a",
           "#d78fcd",
           "#64ca4a",
           "#51ebde",
           "#cbab53",
           "#bc9c60",
           "#d6e2b6",
           "#e5eb9a",
           "#aad380",
           "#d3ad27",
           "#94ef6f",
           "#d3e731",
           "#f7673f",
           "#2fc2b0",
           "#858cf5",
           "#c773f4",
           "#9fc6a6",
           "#95a256",
           "#e778c3",
           "#7db629",
           "#63beb4",
           "#ef9cee",
           "#ec70d6",
           "#eb8544",
           "#e74eae",
           "#a1b1ec",
           "#f06fa4",
           "#ebbecf",
           "#cc80ad",
           "#4368e3",
           "#47b750",
           "#e472eb",
           "#eaccad",
           "#efb11f",
           "#85ab79",
           "#c2987d",
           "#e99d7e",
           "#78a795",
           "#e794b3",
           "#89e9eb",
           "#c4922b",
           "#e48586",
           "#59d1a6",
           "#7c9aed",
           "#9fbf2a",
           "#e5e54d",
           "#51a5ee",
           "#ddd467",
           "#47f1c1",
           "#eec456",
           "#8b9bc7",
           "#89efb6",
           "#ee7b25",
           "#aae634",
           "#c2f1ac",
           "#edc57a",
           "#5ec7ea",
           "#47b273",
           "#f94db6",
           "#93e98e",
           "#aba580",
           "#35c039",
           "#73b25f",
           "#50ec73",
           "#d98f5e",
           "#b09d32",
           "#58a9b4",
           "#85b344",
           "#a9e85a",
           "#eea554",
           "#4ed2e2")
j = nrow(data)
print(j)
#data3
#cols=c("Proteobacteria"="deepskyblue","Planctomycetes"="chartreuse4","Caldithrix"="cadetblue1","Actinobacteria"="indianred3","Acidobacteria"="burlywood1","Gemmatimonadetes"="blue","Nitrospirae"="aquamarine","Verrucomicrobia"="deeppink","Armatimonadetes"="darkseagreen1","Fusobacteria"="darksalmon","Elusimicrobia"="darkred","Spirochaetes"="darkorchid1","Chlorobi"="darkorange","Lentisphaerae"="darkolivegreen2","Chlamydiae"="darkgreen","Chloroflexi"="cyan","Tenericutes"="darkgray","Firmicutes"="red","Bacteroidetes"="green","Cyanobacteria"="lightpink","Deferribacteres"="lightgoldenrod","Fibrobacteres"="mediumpurple1","Synergistetes"="mediumorchid1","Thermotogae"="lightseagreen","[Thermi]"="lightsalmon","SAR406"="tomato1","other phylum"="coral4","Crenarchaeota"="navyblue","Euryarchaeota"="yellow1","[Parvarchaeota]"="dodgerblue")
gg<-ggplot(data3,aes(x=taxonomy,y=value, fill=variable))
gg1=gg+geom_bar(stat="identity", colour='black', size=0)+
	scale_fill_manual(name="",values = palette[1:j])+
	guides(fill=guide_legend(override.aes=list(colour=NA),ncol=as.numeric(args[3])))+xlab("")+
	ylab("%relative abundance of bacterial phyla")+
	theme(axis.text.x=element_text(angle=45,hjust=1, size=12)) #No line border

gg2=gg1+theme(legend.direction="horizontal", legend.position="right", 
              legend.box="vertical", legend.key.size = unit(1.0, 'lines'),
              legend.title.align=0,legend.text = element_text(size = 9))

#ggsave("Fig2_RelativePhylumModi_3.png", plot=gg2, width=18, height=15, units="cm",dpi=300)

#png(args[2], width = as.numeric(args[4]), height = as.numeric(args[5]), units = "cm", res = 300)

svg(args[2],width=as.numeric(args[4]),height=as.numeric(args[5]))
gg2
dev.off()

